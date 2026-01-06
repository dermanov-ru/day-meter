<?php

use App\Http\Controllers\BiometricController;
use App\Http\Controllers\ChronicleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DiseaseController;
use App\Http\Controllers\EntryController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StatsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/entry/{date?}', [EntryController::class, 'show'])->name('entry.show');
    Route::post('/entry', [EntryController::class, 'store'])->name('entry.store');

    Route::get('/stats/month', [StatsController::class, 'month'])->name('stats.month');

    Route::get('/chronicle', [ChronicleController::class, 'index'])->name('chronicle.index');

    Route::get('/settings/metrics', [SettingsController::class, 'metrics'])->name('settings.metrics');
    Route::post('/settings/metrics', [SettingsController::class, 'storeMetric'])->name('settings.metrics.store');
    Route::patch('/settings/metrics/{metric}', [SettingsController::class, 'updateMetric'])->name('settings.metrics.update');

    Route::get('/settings/categories', [SettingsController::class, 'categories'])->name('settings.categories');
    Route::post('/settings/categories', [SettingsController::class, 'storeCategory'])->name('settings.categories.store');
    Route::patch('/settings/categories/{category}', [SettingsController::class, 'updateCategory'])->name('settings.categories.update');
    Route::delete('/settings/categories/{category}', [SettingsController::class, 'deleteCategory'])->name('settings.categories.delete');

    Route::get('/settings/export', [SettingsController::class, 'export'])->name('settings.export');
    Route::post('/settings/export', [SettingsController::class, 'generateExport'])->name('settings.export.generate');

    Route::get('/reminders', [NotificationController::class, 'remindersPage'])->name('reminders.page');

    // Health / Disease routes
    Route::get('/health/diseases', [DiseaseController::class, 'index'])->name('diseases.index');
    Route::get('/health/diseases/create', [DiseaseController::class, 'create'])->name('diseases.create');
    Route::post('/health/diseases', [DiseaseController::class, 'store'])->name('diseases.store');
    Route::get('/health/diseases/{disease}', [DiseaseController::class, 'show'])->name('diseases.show');
    Route::get('/health/diseases/{disease}/edit', [DiseaseController::class, 'edit'])->name('diseases.edit');
    Route::patch('/health/diseases/{disease}', [DiseaseController::class, 'update'])->name('diseases.update');
    Route::patch('/health/diseases/{disease}/close', [DiseaseController::class, 'close'])->name('diseases.close');
    Route::delete('/health/diseases/{disease}', [DiseaseController::class, 'destroy'])->name('diseases.destroy');
    Route::post('/health/diseases/{disease}/notes', [DiseaseController::class, 'storeNote'])->name('diseases.notes.store');
    Route::delete('/health/diseases/{disease}/notes/{note}', [DiseaseController::class, 'destroyNote'])->name('diseases.notes.destroy');

    // Notification API endpoints
    Route::post('/api/notifications/subscribe', [NotificationController::class, 'subscribe'])->name('notifications.subscribe');
    Route::post('/api/notifications/unsubscribe', [NotificationController::class, 'unsubscribe'])->name('notifications.unsubscribe');
    Route::get('/api/notifications/settings', [NotificationController::class, 'getSettings'])->name('notifications.settings.get');
    Route::post('/api/notifications/settings', [NotificationController::class, 'updateSettings'])->name('notifications.settings.update');

    // Biometric API endpoints
    Route::post('/api/biometric/register/options', [BiometricController::class, 'registerOptions'])->name('biometric.register.options');
    Route::post('/api/biometric/register/verify', [BiometricController::class, 'registerVerify'])->name('biometric.register.verify');
    Route::post('/api/biometric/unlock/options', [BiometricController::class, 'unlockOptions'])->name('biometric.unlock.options');
    Route::post('/api/biometric/unlock/verify', [BiometricController::class, 'unlockVerify'])->name('biometric.unlock.verify');
    Route::post('/api/biometric/disable', [BiometricController::class, 'disable'])->name('biometric.disable');
    Route::get('/api/biometric/status', [BiometricController::class, 'status'])->name('biometric.status');
});

require __DIR__.'/auth.php';
