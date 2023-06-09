<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CredentialController;
use App\Http\Controllers\IncomingLetterController;
use App\Http\Controllers\LetterCategoryController;
use App\Http\Controllers\LetterController;
use App\Http\Controllers\OutgoingLetterController;
use App\Http\Controllers\RoleController;
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
    Route::controller(RoleController::class)->group(function () {
        Route::get("/role", "index");
    });

    Route::controller(UserController::class)->group(function () {
        Route::get("/user/total", "total");
        Route::get("/user", "index");
        Route::get("/user/{user}", "show");
        Route::post("/user", "store");
        Route::put("/user/{user}", "update");
        Route::delete("/user/{user}", "destroy");
    });

    Route::controller(IncomingLetterController::class)->group(function () {
        Route::get("/letter/incoming", "index");
        Route::get("/letter/incoming/report", "report");
        Route::get("/letter/incoming/{incomingLetter}", "show");
        Route::post("/disposition", "disposition");
    });

    Route::controller(OutgoingLetterController::class)->group(function () {
        Route::get("/letter/outgoing", "index");
        Route::get("/letter/outgoing/report", "report");
        Route::get("/letter/outgoing/{outgoingLetter}", "show");
    });

    Route::controller(LetterCategoryController::class)->group(function () {
        Route::get("/letter/category", "index");
    });

    Route::controller(LetterController::class)->group(function () {
        Route::get("/letter/total", "total");
        Route::get("/letter", "index");
        Route::get("/letter/{letter}", "show");
        Route::post("/letter", "store");
        Route::put("/letter/{letter}", "update");
        Route::delete("/letter/{letter}", "destroy");
    });
});
