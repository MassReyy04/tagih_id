<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KolektibilitasController;
use App\Http\Controllers\GeocodingController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserManagementController;
use Illuminate\Support\Facades\Route;

// Redirect root ke halaman login
Route::get('/', fn () => redirect()->route('login'))->name('landing');

Auth::routes(['register' => false]);

Route::middleware('auth')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile/photo', [ProfileController::class, 'destroyPhoto'])->name('profile.photo.destroy');

    Route::get('/kolektibilitas', [KolektibilitasController::class, 'index'])->name('kolektibilitas.index');

    Route::middleware('admin')->group(function () {
        Route::get('/dashboard-admin', [DashboardController::class, 'index'])->name('dashboard.admin');
        Route::get('/admin/users', [UserManagementController::class, 'index'])->name('admin.users.index');
        Route::post('/admin/users', [UserManagementController::class, 'store'])->name('admin.users.store');
        Route::get('/admin/users/{user}/edit', [UserManagementController::class, 'edit'])->name('admin.users.edit');
        Route::put('/admin/users/{user}', [UserManagementController::class, 'update'])->name('admin.users.update');
        Route::delete('/admin/users/{user}', [UserManagementController::class, 'destroy'])->name('admin.users.destroy');

        Route::put('/kolektibilitas/saldo', [KolektibilitasController::class, 'updateSaldo'])->name('kolektibilitas.saldo');
        Route::put('/kolektibilitas/bermasalah', [KolektibilitasController::class, 'updateBermasalah'])->name('kolektibilitas.bermasalah');
        Route::put('/kolektibilitas/mitra', [KolektibilitasController::class, 'updateMitra'])->name('kolektibilitas.mitra');
        Route::delete('/kolektibilitas/{kolektibilitas}', [KolektibilitasController::class, 'destroy'])->name('kolektibilitas.destroy');
    });

    Route::get('geocode/reverse', [GeocodingController::class, 'reverse'])
        ->middleware('throttle:60,1')
        ->name('geocode.reverse');

    Route::get('monitoring/{monitoring}/pdf', [MonitoringController::class, 'pdf'])
        ->name('monitoring.pdf');

    Route::resource('monitoring', MonitoringController::class);
});
