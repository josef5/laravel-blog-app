<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExampleController;
use App\Http\Controllers\UserController;

Route::get('/', [ExampleController::class, 'homepage']);
Route::get('/about', [ExampleController::class, 'about']);

Route::post('/register', [UserController::class, 'register']);
