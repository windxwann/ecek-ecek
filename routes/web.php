<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\Owner;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Tenant;
use Illuminate\Support\Facades\Route;

// Public Marketplace
Route::get('/', [Tenant\MarketplaceController::class, 'home'])->name('home');
Route::get('/motors', [Tenant\MarketplaceController::class, 'index'])->name('marketplace.index');
Route::get('/motors/{motor}', [Tenant\MarketplaceController::class, 'show'])->name('marketplace.show');

// Authentication
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Protected Common Routes
Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Secure Document Downloads
    Route::get('/files/motor-document/{motor}', [FileController::class, 'motorDocument'])->name('files.motor_document');
    Route::get('/files/payment-proof/{transaksi}', [FileController::class, 'paymentProof'])->name('files.payment_proof');

    // Tenant Routes
    Route::middleware('role:penyewa')->prefix('tenant')->name('tenant.')->group(function () {
        Route::get('/dashboard', [Tenant\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/rentals', [Tenant\RentalController::class, 'index'])->name('rentals.index');
        Route::get('/motors/{motor}/rent', [Tenant\RentalController::class, 'create'])->name('rentals.create');
        Route::post('/motors/{motor}/quote', [Tenant\RentalController::class, 'quote'])->name('rentals.quote');
        Route::post('/motors/{motor}/rent', [Tenant\RentalController::class, 'store'])->name('rentals.store');
        Route::get('/rentals/{rental}', [Tenant\RentalController::class, 'show'])->name('rentals.show');
        Route::post('/rentals/{rental}/cancel', [Tenant\RentalController::class, 'cancel'])->name('rentals.cancel');

        Route::get('/payments', [Tenant\PaymentController::class, 'index'])->name('payments.index');
        Route::get('/rentals/{rental}/pay', [Tenant\PaymentController::class, 'create'])->name('payments.create');
        Route::post('/rentals/{rental}/pay', [Tenant\PaymentController::class, 'store'])->name('payments.store');
    });

    // Owner Routes
    Route::middleware('role:pemilik')->prefix('owner')->name('owner.')->group(function () {
        Route::get('/dashboard', [Owner\DashboardController::class, 'index'])->name('dashboard');
        Route::resource('motors', Owner\MotorController::class);
        Route::get('/rentals', [Owner\RentalController::class, 'index'])->name('rentals.index');
        Route::get('/revenue', [Owner\RevenueController::class, 'index'])->name('revenue.index');
    });

    // Admin Routes
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

        // User Management
        Route::resource('users', Admin\UserController::class);
        Route::post('/users/{user}/toggle-active', [Admin\UserController::class, 'toggleActive'])->name('users.toggle_active');

        // Motor Management & Verification
        Route::get('/motors', [Admin\MotorController::class, 'index'])->name('motors.index');
        Route::get('/motors/{motor}', [Admin\MotorController::class, 'show'])->name('motors.show');
        Route::post('/motors/{motor}/verify', [Admin\MotorController::class, 'verify'])->name('motors.verify');
        Route::post('/motors/{motor}/pricing', [Admin\MotorController::class, 'updatePricing'])->name('motors.pricing');
        Route::delete('/motors/{motor}/pricing', [Admin\MotorController::class, 'destroyPricing'])->name('motors.pricing.destroy');
        Route::post('/motors/{motor}/status', [Admin\MotorController::class, 'updateStatus'])->name('motors.status');

        // Rental Management & Returns
        Route::get('/rentals', [Admin\RentalController::class, 'index'])->name('rentals.index');
        Route::get('/rentals/{rental}', [Admin\RentalController::class, 'show'])->name('rentals.show');
        Route::post('/rentals/{rental}/confirm', [Admin\RentalController::class, 'confirm'])->name('rentals.confirm');
        Route::post('/rentals/{rental}/ongoing', [Admin\RentalController::class, 'markOngoing'])->name('rentals.ongoing');
        Route::post('/rentals/{rental}/return', [Admin\RentalController::class, 'confirmReturn'])->name('rentals.return');
        Route::post('/rentals/{rental}/cancel', [Admin\RentalController::class, 'cancel'])->name('rentals.cancel');

        // Payment Management (FR-17: entri manual + CRUD)
        Route::get('/payments', [Admin\PaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/create', [Admin\PaymentController::class, 'create'])->name('payments.create');
        Route::post('/payments', [Admin\PaymentController::class, 'store'])->name('payments.store');
        Route::get('/payments/{transaksi}/edit', [Admin\PaymentController::class, 'edit'])->name('payments.edit');
        Route::put('/payments/{transaksi}', [Admin\PaymentController::class, 'update'])->name('payments.update');
        Route::delete('/payments/{transaksi}', [Admin\PaymentController::class, 'destroy'])->name('payments.destroy');
        Route::post('/payments/{transaksi}/confirm', [Admin\PaymentController::class, 'confirm'])->name('payments.confirm');
        Route::post('/payments/{transaksi}/reject', [Admin\PaymentController::class, 'reject'])->name('payments.reject');

        // Reports
        Route::get('/reports/revenue', [Admin\ReportController::class, 'revenue'])->name('reports.revenue');
        Route::get('/reports/payments', [Admin\ReportController::class, 'payments'])->name('reports.payments');
        Route::get('/reports/profit-sharing', [Admin\ReportController::class, 'profitSharing'])->name('reports.profit_sharing');
        Route::post('/reports/profit-sharing/{bagiHasil}/settle', [Admin\ReportController::class, 'settleProfitSharing'])->name('reports.profit_sharing.settle');
    });
});
