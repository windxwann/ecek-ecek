<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Motor;
use App\Models\Penyewaan;
use App\Models\TarifRental;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MotorController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');
        $search = $request->query('q');

        $motors = Motor::query()
            ->with(['pemilik', 'tarif'])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('merk', 'like', '%'.$search.'%')
                        ->orWhere('no_plat', 'like', '%'.$search.'%')
                        ->orWhereHas('pemilik', fn ($p) => $p->where('nama', 'like', '%'.$search.'%'));
                });
            })
            ->orderBy('id', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.motors.index', [
            'motors' => $motors,
            'currentStatus' => $status,
            'search' => $search,
            'counts' => [
                'pending' => Motor::where('status', Motor::STATUS_PENDING)->count(),
                'tersedia' => Motor::where('status', Motor::STATUS_AVAILABLE)->count(),
                'disewa' => Motor::where('status', Motor::STATUS_RENTED)->count(),
                'total' => Motor::count(),
            ],
        ]);
    }

    public function show(Motor $motor): View
    {
        $motor->load(['pemilik', 'tarif', 'penyewaans.penyewa']);

        return view('admin.motors.show', [
            'motor' => $motor,
        ]);
    }

    public function verify(Request $request, Motor $motor): RedirectResponse
    {
        $data = $request->validate([
            'action' => ['required', 'in:approve,reject'],
            'catatan_admin' => ['nullable', 'string', 'max:500'],
            'tarif_harian' => ['nullable', 'numeric', 'min:0'],
            'tarif_mingguan' => ['nullable', 'numeric', 'min:0'],
            'tarif_bulanan' => ['nullable', 'numeric', 'min:0'],
        ]);

        if ($data['action'] === 'approve') {
            $motor->update([
                'status' => Motor::STATUS_AVAILABLE,
                'catatan_admin' => $data['catatan_admin'] ?? 'Disetujui oleh admin.',
            ]);

            if (isset($data['tarif_harian'])) {
                TarifRental::updateOrCreate(
                    ['motor_id' => $motor->id],
                    [
                        'tarif_harian' => $data['tarif_harian'] ?? 0,
                        'tarif_mingguan' => $data['tarif_mingguan'] ?? 0,
                        'tarif_bulanan' => $data['tarif_bulanan'] ?? 0,
                    ]
                );
            }

            return redirect()->route('admin.motors.index')
                ->with('success', "Motor {$motor->merk} ({$motor->no_plat}) berhasil diverifikasi dan aktif.");
        }

        $motor->update([
            'status' => Motor::STATUS_REJECTED,
            'catatan_admin' => $data['catatan_admin'] ?? 'Dokumen atau data motor tidak memenuhi syarat.',
        ]);

        return redirect()->route('admin.motors.index')
            ->with('info', "Motor {$motor->merk} ({$motor->no_plat}) telah ditolak.");
    }

    public function updatePricing(Request $request, Motor $motor): RedirectResponse
    {
        $data = $request->validate([
            'tarif_harian' => ['required', 'numeric', 'min:1000'],
            'tarif_mingguan' => ['required', 'numeric', 'min:1000'],
            'tarif_bulanan' => ['required', 'numeric', 'min:1000'],
        ]);

        TarifRental::updateOrCreate(
            ['motor_id' => $motor->id],
            $data
        );

        return back()->with('success', "Tarif sewa untuk {$motor->merk} berhasil diperbarui.");
    }

    public function destroyPricing(Motor $motor): RedirectResponse
    {
        if (! $motor->tarif) {
            return back()->with('error', "Motor {$motor->merk} belum memiliki tarif rental.");
        }

        if ($motor->penyewaans()
            ->whereIn('status', [Penyewaan::STATUS_CONFIRMED, Penyewaan::STATUS_ONGOING])
            ->exists()) {
            return back()->with('error', 'Tarif tidak dapat dihapus karena motor memiliki penyewaan aktif.');
        }

        $motor->tarif->delete();

        // Motor tanpa tarif tidak boleh tetap tampil sebagai unit yang bisa disewa.
        if ($motor->status === Motor::STATUS_AVAILABLE) {
            $motor->update(['status' => Motor::STATUS_INACTIVE]);
        }

        return back()->with('success', "Tarif rental untuk {$motor->merk} berhasil dihapus.");
    }

    public function updateStatus(Request $request, Motor $motor): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(Motor::STATUSES)],
        ]);

        $motor->update(['status' => $data['status']]);

        return back()->with('success', "Status motor berhasil diubah menjadi {$motor->status_label}.");
    }
}
