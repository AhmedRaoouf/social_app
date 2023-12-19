<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\PostController;
use App\Http\Controllers\api\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post("/register", [AuthController::class, "register"]);
Route::post("/login",[AuthController::class, "login"])->name('login');

Route::post('/forget',[AuthController::class,'forget']);
Route::post('/otp/{otp}',[AuthController::class,'otp']);
Route::post('/reset/{otp}',[AuthController::class,'reset']);

Route::middleware(['api_auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    //Profile
    Route::get('/profile/show', [ProfileController::class, 'show']);
    Route::post('/profile/update', [ProfileController::class, 'update']);
    Route::post('/profile/update_password', [ProfileController::class, 'update_password']);
    Route::delete('/profile/delete', [ProfileController::class, 'delete_account']);

    //Posts
    Route::post('/user/post/create', [PostController::class, 'createPost']);
    Route::post('/user/{userId}/posts/{postId}/react', [PostController::class, 'reactToPost']);
    Route::post('/user/{userId}/posts/{postId}/comment', [PostController::class, 'addCommentToPost']);
    Route::get('/user/{userId}/posts', [PostController::class, 'getUserPosts']); #Show other user posts
});

Route::post('/email/verify', [AuthController::class,'sendVerificationLink']);
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verify'])->name('verification.verify');
