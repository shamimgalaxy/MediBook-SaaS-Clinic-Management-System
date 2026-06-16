<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use App\Http\Controllers\Tenant\DoctorController;
use App\Http\Controllers\Tenant\AppointmentController;
use App\Http\Controllers\Tenant\InvoiceController;
use App\Http\Controllers\Tenant\DashboardController;
use App\Http\Controllers\Tenant\PrescriptionController;
use App\Http\Controllers\Tenant\ReportController;
use App\Http\Controllers\Tenant\SubscriptionController;
use App\Http\Controllers\Tenant\PatientController;
use App\Http\Controllers\Tenant\SettingsController;
use App\Http\Controllers\Tenant\NotificationController;
use App\Http\Controllers\Tenant\ProfileController;

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {

    Route::get('/', function () {
        return 'Welcome to ' . tenant('clinic_name');
    });


    // ─────────────────────────────────────────────────────────────────────

    require __DIR__.'/auth.php';

    // ── Subscription routes (no check.subscription — needed for expired clinics) ──
    Route::middleware(['auth', 'verified'])->group(function () {

        Route::middleware(['role:clinic_admin'])
            ->prefix('subscription')->name('subscription.')->group(function () {
            Route::get('/',     [SubscriptionController::class, 'index'])->name('index');
            Route::post('/pay', [SubscriptionController::class, 'initiate'])->name('pay');
        });

    });

    // SSLCommerz callbacks (no auth)
    Route::post('/subscription/success', [SubscriptionController::class, 'success'])->name('subscription.success');
    Route::post('/subscription/fail',    [SubscriptionController::class, 'fail'])->name('subscription.fail');
    Route::post('/subscription/cancel',  [SubscriptionController::class, 'cancel'])->name('subscription.cancel');
    Route::post('/subscription/ipn',     [SubscriptionController::class, 'ipn'])->name('subscription.ipn');

    // ── All other routes require auth + active subscription ───────────────
    Route::middleware(['auth', 'verified', 'check.subscription'])->group(function () {

        // ─── Dashboard redirect ───────────────────────────────────────────
        Route::get('/dashboard', function () {
            $user = auth()->user();
            $role = $user->role ?? null;

            if ($user->hasRole('clinic_admin') || $role === 'clinic_admin')
                return redirect()->route('admin.dashboard');

            if ($user->hasRole('doctor') || $role === 'doctor')
                return redirect()->route('doctor.dashboard');

            if ($user->hasRole('receptionist') || $role === 'receptionist')
                return redirect()->route('receptionist.dashboard');

            return redirect()->route('patient.dashboard');
        })->name('dashboard');

        // ─── Notifications ────────────────────────────────────────────────
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
        Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.readAll');

        // ─── Profile (non-patient) ────────────────────────────────────────
        Route::get('/profile',          [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile',          [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

        // ─── Clinic Admin ─────────────────────────────────────────────────
        Route::middleware(['role:clinic_admin'])->prefix('admin')->name('admin.')->group(function () {
            Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

            Route::prefix('doctors')->name('doctors.')->group(function () {
                Route::get('/',                  [DoctorController::class, 'index'])->name('index');
                Route::get('/create',            [DoctorController::class, 'create'])->name('create');
                Route::post('/',                 [DoctorController::class, 'store'])->name('store');
                Route::get('/{doctor}',          [DoctorController::class, 'show'])->name('show');
                Route::get('/{doctor}/edit',     [DoctorController::class, 'edit'])->name('edit');
                Route::put('/{doctor}',          [DoctorController::class, 'update'])->name('update');
                Route::delete('/{doctor}',       [DoctorController::class, 'destroy'])->name('destroy');
                Route::patch('/{doctor}/toggle', [DoctorController::class, 'toggleStatus'])->name('toggle');
            });
        });

        // ─── Doctor ───────────────────────────────────────────────────────
        Route::middleware(['role:doctor'])->prefix('doctor')->name('doctor.')->group(function () {
            Route::get('/dashboard', fn() => 'Doctor Dashboard — ' . tenant('clinic_name'))
                ->name('dashboard');
        });

        // ─── Receptionist ─────────────────────────────────────────────────
        Route::middleware(['role:receptionist'])->prefix('receptionist')->name('receptionist.')->group(function () {
            Route::get('/dashboard', fn() => 'Receptionist Dashboard — ' . tenant('clinic_name'))
                ->name('dashboard');
        });

        // ─── Patient ──────────────────────────────────────────────────────
        Route::middleware(['role:patient'])->prefix('patient')->name('patient.')->group(function () {
            Route::get('/dashboard',        [PatientController::class, 'dashboard'])->name('dashboard');
            Route::get('/book',             [PatientController::class, 'book'])->name('book');
            Route::get('/history',          [PatientController::class, 'history'])->name('history');
            Route::get('/profile',          [PatientController::class, 'profile'])->name('profile');
            Route::put('/profile',          [PatientController::class, 'updateProfile'])->name('profile.update');
            Route::put('/profile/password', [PatientController::class, 'updatePassword'])->name('profile.password');
        });

        // ─── Appointments ─────────────────────────────────────────────────
        Route::middleware(['role:clinic_admin|receptionist|doctor|patient'])
            ->prefix('appointments')->name('appointments.')->group(function () {

            Route::get('/',                   [AppointmentController::class, 'index'])->name('index');
            Route::get('/create',             [AppointmentController::class, 'create'])->name('create');
            Route::post('/',                  [AppointmentController::class, 'store'])->name('store');
            Route::get('/{appointment}',      [AppointmentController::class, 'show'])->name('show');
            Route::get('/{appointment}/edit', [AppointmentController::class, 'edit'])->name('edit');
            Route::put('/{appointment}',      [AppointmentController::class, 'update'])->name('update');
            Route::delete('/{appointment}',   [AppointmentController::class, 'destroy'])->name('destroy');

            Route::patch('/{appointment}/status',
                [AppointmentController::class, 'updateStatus'])->name('updateStatus');
            Route::patch('/{appointment}/payment',
                [AppointmentController::class, 'updatePayment'])->name('updatePayment');

            Route::get('/{appointment}/prescription/create',
                [PrescriptionController::class, 'create'])->name('prescription.create');
            Route::post('/{appointment}/prescription',
                [PrescriptionController::class, 'store'])->name('prescription.store');
        });

        // ─── Invoices ─────────────────────────────────────────────────────
        Route::middleware(['role:clinic_admin|receptionist|patient'])
            ->prefix('invoices')->name('invoices.')->group(function () {

            Route::get('/',              [InvoiceController::class, 'index'])->name('index');
            Route::get('/{invoice}',     [InvoiceController::class, 'show'])->name('show');
            Route::get('/{invoice}/pdf', [InvoiceController::class, 'downloadPdf'])->name('pdf');
        });

        // ─── Prescriptions ────────────────────────────────────────────────
        Route::middleware(['role:clinic_admin|receptionist|doctor|patient'])
            ->prefix('prescriptions')->name('prescriptions.')->group(function () {

            Route::get('/',                    [PrescriptionController::class, 'index'])->name('index');
            Route::get('/{prescription}',      [PrescriptionController::class, 'show'])->name('show');
            Route::get('/{prescription}/edit', [PrescriptionController::class, 'edit'])->name('edit');
            Route::put('/{prescription}',      [PrescriptionController::class, 'update'])->name('update');
            Route::get('/{prescription}/pdf',  [PrescriptionController::class, 'downloadPdf'])->name('pdf');
        });

        // ─── Reports (Clinic Admin only) ──────────────────────────────────
        Route::middleware(['role:clinic_admin'])
            ->prefix('reports')->name('reports.')->group(function () {

            Route::get('/revenue',             [ReportController::class, 'revenue'])->name('revenue');
            Route::get('/appointments',        [ReportController::class, 'appointments'])->name('appointments');
            Route::get('/doctors',             [ReportController::class, 'doctors'])->name('doctors');
            Route::get('/revenue/export',      [ReportController::class, 'exportRevenue'])->name('revenue.export');
            Route::get('/appointments/export', [ReportController::class, 'exportAppointments'])->name('appointments.export');
        });

        // ─── Settings (Clinic Admin only) ─────────────────────────────────
        Route::middleware(['role:clinic_admin'])
            ->prefix('settings')->name('settings.')->group(function () {

            Route::get('/',               [SettingsController::class, 'index'])->name('index');
            Route::post('/general',       [SettingsController::class, 'updateGeneral'])->name('general');
            Route::post('/hours',         [SettingsController::class, 'updateHours'])->name('hours');
            Route::post('/notifications', [SettingsController::class, 'updateNotifications'])->name('notifications');
            Route::delete('/logo',        [SettingsController::class, 'deleteLogo'])->name('logo.delete');
        });

    });
});