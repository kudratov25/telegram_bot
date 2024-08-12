<?php

use App\Http\Controllers\MainController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::get('/web', [MainController::class, 'index']);
Route::post('/order', [OrderController::class, 'store']);
