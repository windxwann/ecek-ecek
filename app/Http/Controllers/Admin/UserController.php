<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BagiHasil;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $role = $request->query('role');
        $search = $request->query('q');

        $roleCounts = [
            'admin' => User::where('role', User::ROLE_ADMIN)->count(),
            'pemilik' => User::where('role', User::ROLE_PEMILIK)->count(),
            'penyewa' => User::where('role', User::ROLE_PENYEWA)->count(),
            'total' => User::count(),
        ];

        $users = User::query()
            ->when($role, fn ($q) => $q->where('role', $role))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('nama', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%')
                        ->orWhere('no_tlpn', 'like', '%'.$search.'%');
                });
            })
            ->orderBy('id', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'currentRole' => $role,
            'search' => $search,
            'roleCounts' => $roleCounts,
        ]);
    }

    public function show(User $user): View
    {
        $user->loadCount(['motors', 'penyewaans']);

        $motors = $user->isPemilik()
            ? $user->motors()->with('tarif')->latest()->limit(5)->get()
            : collect();

        $rentals = $user->isPenyewa()
            ? $user->penyewaans()->with(['motor', 'transaksiTerakhir'])->latest()->limit(5)->get()
            : collect();

        $ownerRevenue = $user->isPemilik()
            ? (float) BagiHasil::query()
                ->whereHas('penyewaan.motor', fn ($q) => $q->where('pemilik_id', $user->id))
                ->sum('bagi_hasil_pemilik')
            : 0.0;

        $totalSpent = $user->isPenyewa()
            ? (float) Transaksi::query()
                ->whereHas('penyewaan', fn ($q) => $q->where('penyewa_id', $user->id))
                ->where('status', Transaksi::STATUS_PAID)
                ->sum('jumlah')
            : 0.0;

        return view('admin.users.show', [
            'user' => $user,
            'motors' => $motors,
            'rentals' => $rentals,
            'ownerRevenue' => $ownerRevenue,
            'totalSpent' => $totalSpent,
        ]);
    }

    public function create(): View
    {
        return view('admin.users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'no_tlpn' => ['required', 'string', 'max:20'],
            'role' => ['required', Rule::in([User::ROLE_ADMIN, User::ROLE_PEMILIK, User::ROLE_PENYEWA])],
            'password' => ['required', 'string', 'min:8'],
            'is_active' => ['required', 'boolean'],
        ]);

        $data['password'] = Hash::make($data['password']);

        User::create($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna baru berhasil ditambahkan.');
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', [
            'user' => $user,
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'no_tlpn' => ['required', 'string', 'max:20'],
            'role' => ['sometimes', Rule::in([User::ROLE_ADMIN, User::ROLE_PEMILIK, User::ROLE_PENYEWA])],
            'password' => ['nullable', 'string', 'min:8'],
            'is_active' => ['required', 'boolean'],
        ]);

        // Role terkunci (disabled di form), pertahankan role lama jika tidak dikirim.
        if (! isset($data['role'])) {
            $data['role'] = $user->role;
        }

        // Admin tidak boleh mengubah role akun adminnya sendiri menjadi non-admin.
        if ($user->id === auth()->id() && $user->isAdmin() && $data['role'] !== User::ROLE_ADMIN) {
            return back()->with('error', 'Anda tidak dapat mengubah role akun admin Anda sendiri.');
        }

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function toggleActive(User $user): RedirectResponse
    {
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun sendiri.');
        }

        $user->update(['is_active' => ! $user->is_active]);

        $statusText = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Akun pengguna {$user->nama} berhasil {$statusText}.");
    }

    public function destroy(User $user): RedirectResponse
    {
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        // Admin terakhir tidak boleh dihapus agar selalu ada backup admin.
        if ($user->isAdmin() && User::where('role', User::ROLE_ADMIN)->count() <= 1) {
            return back()->with('error', 'Minimal harus ada satu akun admin. Tidak dapat menghapus admin terakhir.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna berhasil dihapus.');
    }
}
