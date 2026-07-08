<?php

use App\Http\Controllers\Admin\AppointmentController as AdminAppointmentController;
use App\Http\Controllers\Admin\BlockedDayController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\ProfessionalController;
use App\Http\Controllers\Admin\RolePermissionController;
use App\Http\Controllers\Admin\AppointmentTypeController;
use App\Http\Controllers\PatientAuthController;
use App\Http\Controllers\PatientProfileController;
use App\Http\Controllers\PatientDashboardController;
use Illuminate\Support\Facades\Route;

// Landing principal
Route::get('/', function () {
    return view('welcome');
});

// --- RUTAS PÚBLICAS DEL TURNERO Y PORTAL DE PACIENTES ---
Route::prefix('booking/{slug}')->group(function () {
    Route::get('/', [BookingController::class, 'show'])->name('booking.show');
    Route::get('/slots', [BookingController::class, 'getSlots'])->name('booking.slots');
    Route::get('/month-availability', [BookingController::class, 'getMonthAvailability'])->name('booking.month-availability');
    Route::post('/lock', [BookingController::class, 'lockSlot'])->name('booking.lock');
    Route::post('/confirm', [BookingController::class, 'confirm'])->name('booking.confirm');
    Route::get('/success/{appointment}', [BookingController::class, 'success'])->name('booking.success');
    Route::get('/payment-success', [BookingController::class, 'paymentSuccess'])->name('booking.payment-success');
    Route::post('/mp-webhook', [BookingController::class, 'webhook'])->name('booking.mp-webhook');

    // Autenticación de Pacientes
    Route::get('/login', [PatientAuthController::class, 'showLogin'])->name('booking.login');
    Route::post('/login', [PatientAuthController::class, 'login'])->name('booking.login.submit');
    Route::get('/register', [PatientAuthController::class, 'showRegister'])->name('booking.register');
    Route::post('/register', [PatientAuthController::class, 'register'])->name('booking.register.submit');
    Route::post('/logout', [PatientAuthController::class, 'logout'])->name('booking.logout');

    // Rutas protegidas para Pacientes (Historial de turnos y Perfil de paciente)
    Route::middleware('patient.auth')->group(function () {
        Route::get('/dashboard', [PatientDashboardController::class, 'index'])->name('booking.dashboard');
        Route::get('/profile', [PatientProfileController::class, 'edit'])->name('booking.profile');
        Route::put('/profile', [PatientProfileController::class, 'update'])->name('booking.profile.update');
        Route::put('/profile/password', [PatientProfileController::class, 'updatePassword'])->name('booking.profile.password');
    });
});

// Ruta de cancelación vía email
Route::get('/appointment/cancel/{token}', [BookingController::class, 'cancelForm'])->name('booking.cancel.form');
Route::post('/appointment/cancel/{token}', [BookingController::class, 'cancel'])->name('booking.cancel');

// --- RUTAS ADMINISTRATIVAS ---
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard (Admin y Staff)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Perfil del Administrador / Staff (Usuario)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Gestión de Turnos (Admin y Staff con control de permisos)
    Route::get('/appointments', [AdminAppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/create', [AdminAppointmentController::class, 'create'])->name('appointments.create')->middleware('permission:create_appointments');
    Route::post('/appointments', [AdminAppointmentController::class, 'store'])->name('appointments.store')->middleware('permission:create_appointments');
    Route::get('/appointments/{appointment}/reschedule', [AdminAppointmentController::class, 'rescheduleForm'])->name('appointments.reschedule.form');
    Route::put('/appointments/{appointment}/reschedule', [AdminAppointmentController::class, 'reschedule'])->name('appointments.reschedule');
    Route::post('/appointments/{appointment}/cancel', [AdminAppointmentController::class, 'cancel'])->name('appointments.cancel')->middleware('permission:cancel_appointments');
    
    // Historial y PDF (Admin y Staff)
    Route::get('/appointments/export', [AdminAppointmentController::class, 'exportPdf'])->name('appointments.export');
    Route::post('/appointments/batch-delete', [AdminAppointmentController::class, 'destroyBatch'])->name('appointments.batch_delete');

    // Configuración de Horarios de Agenda, Días Bloqueados y Empresa (Acceso controlado por permisos de la Company)
    Route::get('/schedule', [ScheduleController::class, 'edit'])->name('schedule.edit')->middleware('permission:manage_schedules');
    Route::put('/schedule', [ScheduleController::class, 'update'])->name('schedule.update')->middleware('permission:manage_schedules');
    
    Route::get('/blocked-days', [BlockedDayController::class, 'index'])->name('blocked-days.index')->middleware('permission:manage_blocked_days');
    Route::post('/blocked-days', [BlockedDayController::class, 'store'])->name('blocked-days.store')->middleware('permission:manage_blocked_days');
    Route::delete('/blocked-days/{blockedDay}', [BlockedDayController::class, 'destroy'])->name('blocked-days.destroy')->middleware('permission:manage_blocked_days');
    
    Route::get('/company', [CompanyController::class, 'edit'])->name('company.edit')->middleware('permission:edit_company_info');
    Route::put('/company', [CompanyController::class, 'update'])->name('company.update')->middleware('permission:edit_company_info');

    // Configuración de Permisos y ABM de Profesionales (Exclusivo para Admin y Doctor Admin)
    Route::middleware('role:admin,doctor_admin')->group(function () {
        Route::resource('professionals', ProfessionalController::class)->except(['show']);
        Route::resource('appointment-types', AppointmentTypeController::class)->except(['show']);
        Route::get('/permissions', [RolePermissionController::class, 'index'])->name('permissions.index');
        Route::put('/permissions', [RolePermissionController::class, 'update'])->name('permissions.update');
    });
});

// Alias para compatibilidad con Laravel Breeze
Route::get('/dashboard', function () {
    return redirect()->route('admin.dashboard');
})->name('dashboard');

require __DIR__.'/auth.php';
