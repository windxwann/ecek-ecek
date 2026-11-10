<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OwnerController;
use App\Http\Controllers\Api\TenantController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| REST API (PRD v1 - Bagian 9)
|--------------------------------------------------------------------------
| Prefix utama: /api
| Alias /api/v1 tetap disediakan agar integrasi lama tidak rusak.
*/

$registerRoutes = function (): void {
    // Auth
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);

    // Katalog motor publik (Penyewa)
    Route::get('/motors', [TenantController::class, 'catalog']);
    Route::get('/motors/{motor}', [TenantController::class, 'showMotor']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/user', [AuthController::class, 'user']);

        // Pemilik
        Route::middleware('role:pemilik')->prefix('owner')->group(function () {
            Route::get('/motors', [OwnerController::class, 'motors']);
            Route::post('/motors', [OwnerController::class, 'storeMotor']);
            Route::get('/motors/{motor}', [OwnerController::class, 'showMotor']);
            Route::patch('/motors/{motor}', [OwnerController::class, 'updateMotor']);
            Route::delete('/motors/{motor}', [OwnerController::class, 'destroyMotor']);
            Route::get('/rentals', [OwnerController::class, 'rentals']);
            Route::get('/revenue', [OwnerController::class, 'revenue']);
            Route::get('/profit-sharing', [OwnerController::class, 'profitSharing']);
        });

        // Penyewa
        Route::middleware('role:penyewa')->group(function () {
            Route::post('/bookings', [TenantController::class, 'storeRental']);
            Route::get('/bookings/history', [TenantController::class, 'rentals']);
            Route::get('/bookings/{rental}', [TenantController::class, 'showRental']);
            Route::post('/payments', [TenantController::class, 'storePayment']);
            Route::get('/payments', [TenantController::class, 'payments']);

            // Alias lama
            Route::prefix('tenant')->group(function () {
                Route::post('/rentals', [TenantController::class, 'storeRental']);
                Route::get('/rentals', [TenantController::class, 'rentals']);
                Route::get('/rentals/{rental}', [TenantController::class, 'showRental']);
                Route::post('/payments', [TenantController::class, 'storePayment']);
                Route::get('/payments', [TenantController::class, 'payments']);
            });
        });

        // Admin
        Route::middleware('role:admin')->prefix('admin')->group(function () {
            Route::get('/users', [AdminController::class, 'users']);
            Route::get('/motors', [AdminController::class, 'motors']);
            Route::patch('/motors/{motor}/verify', [AdminController::class, 'verifyMotor']);
            Route::patch('/motors/{motor}/pricing', [AdminController::class, 'updatePricing']);

            Route::get('/bookings', [AdminController::class, 'rentals']);
            Route::patch('/bookings/{rental}/return', [AdminController::class, 'returnRental']);

            Route::get('/rentals', [AdminController::class, 'rentals']);
            Route::patch('/rentals/{rental}/return', [AdminController::class, 'returnRental']);

            Route::get('/payments', [AdminController::class, 'payments']);
            Route::patch('/payments/{transaksi}/confirm', [AdminController::class, 'confirmPayment']);

            Route::get('/reports/revenue', [AdminController::class, 'revenueReport']);
            Route::get('/reports/profit-sharing', [AdminController::class, 'profitSharingReport']);
        });

        // Alias lama tanpa prefix /v1
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
    });
};

Route::group([], $registerRoutes);

// Backward compatibility untuk integrasi yang masih memakai /api/v1.
Route::prefix('v1')->group($registerRoutes);
