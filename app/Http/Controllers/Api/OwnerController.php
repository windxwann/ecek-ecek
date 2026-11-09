<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BagiHasil;
use App\Models\Motor;
use App\Models\Penyewaan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class OwnerController extends Controller
{
    public function motors(Request $request): JsonResponse
    {
        $motors = Motor::where('pemilik_id', $request->user()->id)
            ->with('tarif')
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $motors,
        ]);
    }

    public function storeMotor(Request $request): JsonResponse
    {
        $data = $request->validate([
            'merk' => ['required', 'string', 'max:255'],
            'tipe_cc' => ['required', Rule::in(['100', '125', '150'])],
            'no_plat' => ['required', 'string', 'max:20', 'unique:motors,no_plat'],
            'photo' => ['nullable', 'image', 'max:2048'],
            'dokumen_kepemilikan' => ['nullable', 'file', 'max:5120'],
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('motors', 'public');
        }

        $docPath = null;
        if ($request->hasFile('dokumen_kepemilikan')) {
            $docPath = $request->file('dokumen_kepemilikan')->store('documents', 'local');
        }

        $motor = Motor::create([
            'pemilik_id' => $request->user()->id,
            'merk' => $data['merk'],
            'tipe_cc' => $data['tipe_cc'],
            'no_plat' => strtoupper($data['no_plat']),
            'status' => Motor::STATUS_PENDING,
            'photo' => $photoPath,
            'dokumen_kepemilikan' => $docPath,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Motor berhasil didaftarkan.',
            'data' => $motor,
        ], 201);
    }

    public function showMotor(Request $request, Motor $motor): JsonResponse
    {
        abort_unless($motor->pemilik_id === $request->user()->id, 403);

        $motor->load(['tarif', 'penyewaans']);

        return response()->json([
            'success' => true,
            'data' => $motor,
        ]);
    }

    public function updateMotor(Request $request, Motor $motor): JsonResponse
    {
        abort_unless($motor->pemilik_id === $request->user()->id, 403);

        $data = $request->validate([
            'merk' => ['sometimes', 'string', 'max:255'],
            'tipe_cc' => ['sometimes', Rule::in(['100', '125', '150'])],
            'no_plat' => ['sometimes', 'string', 'max:20', Rule::unique('motors', 'no_plat')->ignore($motor->id)],
        ]);

        if (isset($data['no_plat'])) {
            $data['no_plat'] = strtoupper($data['no_plat']);
        }

        $motor->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Motor berhasil diperbarui.',
            'data' => $motor,
        ]);
    }

    public function destroyMotor(Request $request, Motor $motor): JsonResponse
    {
        abort_unless($motor->pemilik_id === $request->user()->id, 403);

        if ($motor->status === Motor::STATUS_RENTED) {
            return response()->json([
                'success' => false,
                'message' => 'Motor yang sedang disewa tidak dapat dihapus.',
            ], 422);
        }

        $motor->delete();

        return response()->json([
            'success' => true,
            'message' => 'Motor berhasil dihapus.',
        ]);
    }

    public function rentals(Request $request): JsonResponse
    {
        $rentals = Penyewaan::whereHas('motor', fn ($q) => $q->where('pemilik_id', $request->user()->id))
            ->with(['motor', 'penyewa', 'transaksiTerakhir'])
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $rentals,
        ]);
    }

    public function revenue(Request $request): JsonResponse
    {
        $user = $request->user();

        $totalRevenue = (float) BagiHasil::whereHas('penyewaan.motor', fn ($q) => $q->where('pemilik_id', $user->id))->sum('bagi_hasil_pemilik');
        $settledRevenue = (float) BagiHasil::whereHas('penyewaan.motor', fn ($q) => $q->where('pemilik_id', $user->id))->whereNotNull('settled_at')->sum('bagi_hasil_pemilik');

        return response()->json([
            'success' => true,
            'data' => [
                'total_revenue' => $totalRevenue,
                'settled_revenue' => $settledRevenue,
                'pending_revenue' => $totalRevenue - $settledRevenue,
            ],
        ]);
    }

    public function profitSharing(Request $request): JsonResponse
    {
        $profitSharing = BagiHasil::whereHas('penyewaan.motor', fn ($q) => $q->where('pemilik_id', $request->user()->id))
            ->with(['penyewaan.motor'])
            ->latest('tanggal')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $profitSharing,
        ]);
    }
}
