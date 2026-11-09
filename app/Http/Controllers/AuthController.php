<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ], [], [
            'email' => 'email',
            'password' => 'password',
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'Email atau password yang Anda masukkan salah.',
            ]);
        }

        if (! $request->user()->is_active) {
            Auth::logout();

            throw ValidationException::withMessages([
                'email' => 'Akun Anda dinonaktifkan. Hubungi admin untuk informasi lebih lanjut.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()
            ->intended($request->user()->dashboardRoute())
            ->with('success', 'Selamat datang kembali, '.$request->user()->nama.'!');
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'no_tlpn' => ['required', 'string', 'max:20'],
            'role' => ['required', Rule::in([User::ROLE_PEMILIK, User::ROLE_PENYEWA])],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [], [
            'nama' => 'nama',
            'no_tlpn' => 'nomor telepon',
            'role' => 'peran',
            'password' => 'password',
        ]);

        $user = User::create([
            'nama' => $data['nama'],
            'email' => $data['email'],
            'no_tlpn' => $data['no_tlpn'],
            'role' => $data['role'],
            'password' => Hash::make($data['password']),
            'is_active' => true,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()
            ->route($user->dashboardRoute() === route('tenant.dashboard') ? 'tenant.dashboard' : 'owner.dashboard')
            ->with('success', 'Akun berhasil dibuat. Selamat bergabung di MOTORRENT!');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Anda telah keluar dari akun.');
    }
}
