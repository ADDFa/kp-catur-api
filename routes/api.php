<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CredentialController;
use App\Http\Controllers\IncomingLetterController;
use App\Http\Controllers\LetterController;
use App\Http\Controllers\NumberOfLetterController;
use App\Http\Controllers\OutgoingLetterController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::controller(AuthController::class)->group(function () {
    Route::post("/login", "login");
    Route::post("/refresh", "refreshToken");
});

Route::middleware(Auth::class)->group(function () {
    Route::controller(UserController::class)->group(function () {
        Route::get("/user", "index");
        Route::get("/user/{user}", "show");
        Route::post("/user", "store");
        Route::delete("/user/{user}", "destroy");
    });

    Route::controller(CredentialController::class)->group(function () {
        Route::put("/account", "update");
    });

    Route::controller(LetterController::class)->group(function () {
        Route::get("/letter/{type}", "index");
        Route::get("/letter/{type}/{letter}", "show");
        Route::post("/letter/{type}", "store");
        Route::put("/letter/{type}/{letter}", "update");
        Route::delete("/letter/{type}/{letter}", "destroy");
    });

    Route::controller(IncomingLetterController::class)->group(function () {
        Route::post("/disposition", "disposition");
    });

    Route::controller(NumberOfLetterController::class)->group(function () {
        Route::get("/number-of-letters", "show");
    });
});
