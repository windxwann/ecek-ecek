<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Motor;
use App\Models\Penyewaan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MotorController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');

        $motors = Motor::query()
            ->where('pemilik_id', $request->user()->id)
            ->with(['tarif'])
            ->withCount('penyewaans')
            ->when($status, fn ($q) => $q->where('status', $status))
            ->orderBy('id', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('owner.motors.index', [
            'motors' => $motors,
            'currentStatus' => $status,
        ]);
    }

    public function create(): View
    {
        return view('owner.motors.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'merk' => ['required', 'string', 'max:255'],
            'tipe_cc' => ['required', Rule::in(['100', '125', '150'])],
            'no_plat' => ['required', 'string', 'max:20', 'unique:motors,no_plat'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'dokumen_kepemilikan' => ['required', 'file', 'mimes:pdf,jpeg,png,jpg', 'max:5120'],
        ], [], [
            'merk' => 'merk motor',
            'tipe_cc' => 'tipe CC',
            'no_plat' => 'nomor plat polisi',
            'photo' => 'foto motor',
            'dokumen_kepemilikan' => 'dokumen kepemilikan (STNK/BPKB)',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('motors', 'public');
        }

        $documentPath = $request->file('dokumen_kepemilikan')->store('documents', 'local');

        $motor = Motor::create([
            'pemilik_id' => $request->user()->id,
            'merk' => $data['merk'],
            'tipe_cc' => $data['tipe_cc'],
            'no_plat' => strtoupper($data['no_plat']),
            'status' => Motor::STATUS_PENDING,
            'photo' => $photoPath,
            'dokumen_kepemilikan' => $documentPath,
        ]);

        return redirect()->route('owner.motors.index')
            ->with('success', 'Motor berhasil didaftarkan! Admin akan segera melakukan verifikasi dan menetapkan tarif rental.');
    }

    public function show(Request $request, Motor $motor): View
    {
        abort_unless($motor->pemilik_id === $request->user()->id, 403);

        $motor->load(['tarif', 'penyewaans' => fn ($q) => $q->with('penyewa')->latest()->limit(10)]);

        return view('owner.motors.show', [
            'motor' => $motor,
        ]);
    }

    public function edit(Request $request, Motor $motor): View|RedirectResponse
    {
        abort_unless($motor->pemilik_id === $request->user()->id, 403);

        if ($motor->status === Motor::STATUS_RENTED) {
            return redirect()->route('owner.motors.index')
                ->with('error', 'Motor yang sedang disewa tidak dapat diubah.');
        }

        return view('owner.motors.edit', [
            'motor' => $motor,
        ]);
    }

    public function update(Request $request, Motor $motor): RedirectResponse
    {
        abort_unless($motor->pemilik_id === $request->user()->id, 403);

        if ($motor->status === Motor::STATUS_RENTED) {
            return redirect()->route('owner.motors.index')
                ->with('error', 'Motor yang sedang disewa tidak dapat diubah.');
        }

        $data = $request->validate([
            'merk' => ['required', 'string', 'max:255'],
            'tipe_cc' => ['required', Rule::in(['100', '125', '150'])],
            'no_plat' => ['required', 'string', 'max:20', Rule::unique('motors', 'no_plat')->ignore($motor->id)],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'dokumen_kepemilikan' => ['nullable', 'file', 'mimes:pdf,jpeg,png,jpg', 'max:5120'],
        ], [], [
            'merk' => 'merk motor',
            'tipe_cc' => 'tipe CC',
            'no_plat' => 'nomor plat polisi',
            'photo' => 'foto motor',
            'dokumen_kepemilikan' => 'dokumen kepemilikan',
        ]);

        if ($request->hasFile('photo')) {
            if ($motor->photo) {
                Storage::disk('public')->delete($motor->photo);
            }
            $data['photo'] = $request->file('photo')->store('motors', 'public');
        }

        if ($request->hasFile('dokumen_kepemilikan')) {
            if ($motor->dokumen_kepemilikan) {
                Storage::disk('local')->delete($motor->dokumen_kepemilikan);
            }
            $data['dokumen_kepemilikan'] = $request->file('dokumen_kepemilikan')->store('documents', 'local');
        }

        $data['no_plat'] = strtoupper($data['no_plat']);

        // Jika mengubah plat nomor atau data krusial setelah ditolak/nonaktif, status kembali pending
        if (in_array($motor->status, [Motor::STATUS_REJECTED, Motor::STATUS_INACTIVE], true)) {
            $data['status'] = Motor::STATUS_PENDING;
            $data['catatan_admin'] = null;
        }

        $motor->update($data);

        return redirect()->route('owner.motors.index')
            ->with('success', 'Informasi motor berhasil diperbarui.');
    }

    public function destroy(Request $request, Motor $motor): RedirectResponse
    {
        abort_unless($motor->pemilik_id === $request->user()->id, 403);

        if ($motor->status === Motor::STATUS_RENTED) {
            return redirect()->route('owner.motors.index')
                ->with('error', 'Motor yang sedang disewa tidak dapat dihapus.');
        }

        if ($motor->penyewaans()->whereIn('status', [Penyewaan::STATUS_CONFIRMED, Penyewaan::STATUS_ONGOING])->exists()) {
            return redirect()->route('owner.motors.index')
                ->with('error', 'Motor memiliki transaksi aktif yang belum selesai.');
        }

        if ($motor->photo) {
            Storage::disk('public')->delete($motor->photo);
        }
        if ($motor->dokumen_kepemilikan) {
            Storage::disk('local')->delete($motor->dokumen_kepemilikan);
        }

        $motor->delete();

        return redirect()->route('owner.motors.index')
            ->with('success', 'Motor berhasil dihapus dari sistem.');
    }
}
