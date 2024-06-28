<?php

use App\Events\ChatMessage;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

// User routes
Route::get('/', [UserController::class, 'showCorrectHomepage'])->name('login');
Route::post('/register', [UserController::class, 'register'])->middleware('guest');
Route::post('/login', [UserController::class, 'login'])->middleware('guest');
Route::post('/logout', [UserController::class, 'logout'])->middleware('mustBeLoggedIn');
Route::get('/manage-avatar', [UserController::class, 'showAvatarForm'])->middleware('mustBeLoggedIn');
Route::post('/manage-avatar', [UserController::class, 'storeAvatar'])->middleware('mustBeLoggedIn');

// Follow routes
Route::post('/create-follow/{user:username}', [FollowController::class, 'createFollow'])->middleware('mustBeLoggedIn');
Route::post('/remove-follow/{user:username}', [FollowController::class, 'removeFollow'])->middleware('mustBeLoggedIn');

// Blog routes
Route::get('/create-post', [PostController::class, 'showCreateForm'])->middleware('mustBeLoggedIn');
Route::post('/create-post', [PostController::class, 'storeNewPost'])->middleware('mustBeLoggedIn');
Route::get('/post/{post}', [PostController::class, 'viewSinglePost']);
Route::delete('/post/{post}', [PostController::class, 'deletePost'])->middleware('can:delete,post');
Route::get('/post/{post}/edit', [PostController::class, 'showEditForm'])->middleware('can:update,post');
Route::put('/post/{post}', [PostController::class, 'actuallyUpdate'])->middleware('can:update,post');
Route::get('/search/{term}', [PostController::class, 'search']);

// Profile routes
Route::get('/profile/{user:username}', [UserController::class, 'showProfile'])->middleware('mustBeLoggedIn');
Route::get('/profile/{user:username}/followers', [UserController::class, 'showProfileFollowers'])->middleware('mustBeLoggedIn');
Route::get('/profile/{user:username}/following', [UserController::class, 'showProfileFollowing'])->middleware('mustBeLoggedIn');

Route::middleware('cache.headers:public;max_age=20;etag')->group(function () {
  Route::get('/profile/{user:username}/raw', [UserController::class, 'showProfileRaw'])->middleware('mustBeLoggedIn');
  Route::get('/profile/{user:username}/followers/raw', [UserController::class, 'showProfileFollowersRaw'])->middleware('mustBeLoggedIn');
  Route::get('/profile/{user:username}/following/raw', [UserController::class, 'showProfileFollowingRaw'])->middleware('mustBeLoggedIn');
});

// Route::post('/profile/{user}', [UserController::class, 'updateProfile'])->middleware('mustBeLoggedIn');

// Admin routes
Route::get('/admins-only', function () {
  return 'Only admins should see this page';
})->middleware('can:visitAdminPages');

// Chat routes
Route::post('/send-chat-message', function (Request $request) {
  Log::info('Chat message received: ' . $request->textvalue);

  $formFields = $request->validate([
    'textvalue' => 'required'
  ]);

  if (!trim(strip_tags($formFields['textvalue']))) {
    return response()->noContent();
  }

  broadcast(
    new ChatMessage(
      [
        'username' => auth()->user()->username,
        'textvalue' => strip_tags($request->textvalue),
        'avatar' => auth()->user()->avatar
      ]
    )
  )->toOthers();

  return response()->noContent();
})->middleware('mustBeLoggedIn');
