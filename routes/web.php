<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ToothController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PatientController::class, 'index'])->name('patients.index');

// Randevu ve Takvim Yönetimi (FullCalendar + AJAX)
Route::prefix('appointments')->name('appointments.')->group(function () {
    Route::get('/', [AppointmentController::class, 'index'])->name('index');
    Route::get('/events', [AppointmentController::class, 'getEvents'])->name('events');
    Route::get('/overdue', [AppointmentController::class, 'overdue'])->name('overdue');
    Route::post('/', [AppointmentController::class, 'store'])->name('store');
    Route::post('/{appointment}/reschedule', [AppointmentController::class, 'reschedule'])->name('reschedule');
    Route::post('/{appointment}/status', [AppointmentController::class, 'updateStatus'])->name('update-status');
    Route::patch('/{appointment}/cancel', [PatientController::class, 'cancelAppointment'])->name('cancel');
});

Route::prefix('patients')->name('patients.')->group(function () {
    Route::get('/search', [PatientController::class, 'search'])->name('search');
    Route::post('/', [PatientController::class, 'store'])->name('store');
    Route::get('/{patient}', [PatientController::class, 'show'])->name('show');
    Route::put('/{patient}', [PatientController::class, 'update'])->name('update');
    Route::delete('/{patient}', [PatientController::class, 'destroy'])->name('destroy');

    // İnteraktif Diş Şeması (Odontogram) AJAX Endpoint'leri
    Route::get('/{patient}/teeth/{toothNumber}', [ToothController::class, 'show'])
        ->whereNumber('toothNumber')
        ->name('teeth.show');
    Route::post('/{patient}/teeth', [ToothController::class, 'upsert'])
        ->name('teeth.upsert');

    // Hasta Cari / Tahsilat AJAX Endpoint'i
    Route::post('/{patient}/payments', [FinanceController::class, 'addPayment'])
        ->name('payments.store');
});

// Klinik Finans, Gelir-Gider ve Ay Sonu Muhasebe
Route::prefix('finance')->name('finance.')->group(function () {
    Route::get('/', [FinanceController::class, 'index'])->name('index');
    Route::post('/expenses', [FinanceController::class, 'storeExpense'])->name('expenses.store');
    Route::delete('/expenses/{expense}', [FinanceController::class, 'destroyExpense'])->name('expenses.destroy');
    Route::post('/payments', [FinanceController::class, 'storeQuickPayment'])->name('payments.store');
    Route::delete('/payments/{payment}', [FinanceController::class, 'destroyPayment'])->name('payments.destroy');
    Route::post('/treatment-prices', [FinanceController::class, 'storeTreatmentPrice'])->name('treatment-prices.store');
    Route::put('/treatment-prices/{treatmentPrice}', [FinanceController::class, 'updateTreatmentPrice'])->name('treatment-prices.update');
    Route::delete('/treatment-prices/{treatmentPrice}', [FinanceController::class, 'destroyTreatmentPrice'])->name('treatment-prices.destroy');
});
