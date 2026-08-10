<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\TwoFactorController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
});

Route::get('/home', function () {
    return view('home');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register']);
    Route::get('two-factor/challenge', [TwoFactorController::class, 'showChallenge'])->name('two-factor.challenge');
    Route::post('two-factor/challenge', [TwoFactorController::class, 'verify']);
    Route::post('two-factor/recovery', [TwoFactorController::class, 'useRecoveryCode'])->name('two-factor.recovery');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('two-factor/enable', [TwoFactorController::class, 'showEnable'])->name('two-factor.enable');
    Route::post('two-factor/enable', [TwoFactorController::class, 'enable']);
    Route::post('two-factor/disable', [TwoFactorController::class, 'disable'])->name('two-factor.disable');
});

require __DIR__ . '/admin.php';
require __DIR__ . '/client.php';
