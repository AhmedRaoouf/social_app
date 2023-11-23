<?php

use App\Http\Controllers\api\AuthController;
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
});

Route::post('/email/verify', [AuthController::class,'sendVerificationLink']);
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verify'])->name('verification.verify');
