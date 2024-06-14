<?php

use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

// Auth routes
Route::get('/', [UserController::class, 'showCorrectHomepage'])->name('login');
Route::post('/register', [UserController::class, 'register'])->middleware('guest');
Route::post('/login', [UserController::class, 'login'])->middleware('guest');
Route::post('/logout', [UserController::class, 'logout'])->middleware('mustBeLoggedIn');

// Blog routes
Route::get('/create-post', [PostController::class, 'showCreateForm'])->middleware('mustBeLoggedIn');
Route::post('/create-post', [PostController::class, 'storeNewPost'])->middleware('mustBeLoggedIn');
Route::get('/post/{post}', [PostController::class, 'viewSinglePost']);
Route::delete('/post/{post}', [PostController::class, 'deletePost'])->middleware('can:delete,post');
Route::get('/post/{post}/edit', [PostController::class, 'showEditForm'])->middleware('can:update,post');
Route::put('/post/{post}', [PostController::class, 'actuallyUpdate'])->middleware('can:update,post');

// Profile routes
Route::get('/profile/{user:username}', [UserController::class, 'showProfile']); //->middleware('mustBeLoggedIn');
Route::post('/profile/{user}', [UserController::class, 'updateProfile'])->middleware('mustBeLoggedIn');

// Admin routes
Route::get('/admins-only', function () {
  return 'Only admins should see this page';
})->middleware('can:visitAdminPages');
