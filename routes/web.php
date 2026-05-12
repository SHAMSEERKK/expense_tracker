<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('register', [AuthController::class, 'register']);
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('profile', [AuthController::class, 'profile'])->name('profile.edit');
    Route::put('profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::get('password', [AuthController::class, 'password'])->name('password.edit');
    Route::put('password', [AuthController::class, 'updatePassword'])->name('password.update');
    Route::get('dashboard', DashboardController::class)->name('dashboard');
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('expenses/export', [ExpenseController::class, 'export'])->name('expenses.export');
    Route::resource('expenses', ExpenseController::class)->except(['show']);
    Route::patch('categories/{category}/status', [CategoryController::class, 'changeStatus'])
        ->name('categories.change-status');
    Route::resource('categories', CategoryController::class)->except(['show']);
});
