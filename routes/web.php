<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuperAdmin\DashboardController;
use App\Http\Controllers\SuperAdmin\PlanController;
use App\Http\Controllers\SuperAdmin\TenantController;

// ── Central landing page ──────────────────────────────────────────────────
Route::get('/', function () {
    return view('welcome');
})->name('home');

// ── Super Admin ───────────────────────────────────────────────────────────
Route::prefix('superadmin')->name('superadmin.')->group(function () {

    // Login
    Route::get('/login', function () {
        return 'Super Admin Login Page';
    })->name('login');

    // Logout
    Route::post('/logout', function () {
        auth('super_admin')->logout();
        return redirect()->route('superadmin.login');
    })->name('logout');

    Route::middleware('auth:super_admin')->group(function () {

        // ── Dashboard ─────────────────────────────────────────────────────
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        // ── Plans ─────────────────────────────────────────────────────────
        Route::prefix('plans')->name('plans.')->group(function () {
            Route::get('/',              [PlanController::class, 'index'])->name('index');
            Route::get('/create',        [PlanController::class, 'create'])->name('create');
            Route::post('/',             [PlanController::class, 'store'])->name('store');
            Route::get('/{plan}/edit',   [PlanController::class, 'edit'])->name('edit');
            Route::put('/{plan}',        [PlanController::class, 'update'])->name('update');
            Route::delete('/{plan}',     [PlanController::class, 'destroy'])->name('destroy');
            Route::patch('/{plan}/toggle', [PlanController::class, 'toggle'])->name('toggle');
        });

        // ── Tenants ───────────────────────────────────────────────────────
        Route::prefix('tenants')->name('tenants.')->group(function () {
            Route::get('/',              [TenantController::class, 'index'])->name('index');
            Route::get('/create',        [TenantController::class, 'create'])->name('create');
            Route::post('/',             [TenantController::class, 'store'])->name('store');
            Route::get('/{tenant}',      [TenantController::class, 'show'])->name('show');
            Route::patch('/{tenant}/assign-plan', [TenantController::class, 'assignPlan'])->name('assignPlan');
            Route::patch('/{tenant}/toggle',      [TenantController::class, 'toggle'])->name('toggle');
        });

    });
});

require __DIR__.'/auth.php';