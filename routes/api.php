<?php

use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->patch('categories/{category}/status', [CategoryController::class, 'changeStatus'])
    ->name('api.categories.change-status');
