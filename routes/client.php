<?php

use App\Http\Controllers\Client\DashboardController;
use App\Http\Controllers\Client\InvoiceController;
use App\Http\Controllers\Client\ProfileController;
use App\Http\Controllers\Client\TicketController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:client'])->prefix('cliente')->name('client.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/faturas', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/faturas/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::get('/faturas/{invoice}/download', [InvoiceController::class, 'download'])->name('invoices.download');

    Route::resource('tickets', TicketController::class)->names([
        'index' => 'tickets.index',
        'create' => 'tickets.create',
        'store' => 'tickets.store',
        'show' => 'tickets.show',
    ]);
    Route::post('tickets/{ticket}/reply', [TicketController::class, 'reply'])->name('tickets.reply');
    Route::post('tickets/{ticket}/close', [TicketController::class, 'close'])->name('tickets.close');

    Route::get('/perfil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/perfil', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/perfil/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    Route::get('/notificacoes', [ProfileController::class, 'notifications'])->name('notifications');
    Route::post('/notificacoes/{notification}/read', [ProfileController::class, 'markNotification'])->name('notifications.read');
    Route::post('/notificacoes/read-all', [ProfileController::class, 'markAllNotifications'])->name('notifications.read-all');

    Route::get('/contratos', [ProfileController::class, 'contracts'])->name('contracts');
    Route::get('/contratos/{contract}/pdf', [ProfileController::class, 'contractPdf'])->name('contracts.pdf');
    Route::get('/contratos/{contract}/cancelar', [ProfileController::class, 'cancelRequest'])->name('contracts.cancel');
    Route::post('/contratos/{contract}/cancelar', [ProfileController::class, 'submitCancelRequest'])->name('contracts.cancel-submit');

    Route::get('/velocidade', function () {
        return view('client.speedtest');
    })->name('speedtest');

    Route::get('/pagamentos', [InvoiceController::class, 'payments'])->name('payments');
});
