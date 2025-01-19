<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::get("room-management", [RoomController::class, "index"]);
    Route::prefix("room-management")->group(function () {
        Route::prefix("category")->group(function () {
            Route::post("store", [CategoryController::class, "store"]);
            Route::post("{id}", [CategoryController::class, "update"]);
            Route::delete("{id}", [CategoryController::class, "delete"]);
        });
    });
    Route::get("user-management", [UserController::class, "index"]);
    Route::prefix("user-management")->group(function () {
        Route::post("store", [UserController::class, "store"]);
        Route::post("{id}", [UserController::class, "update"]);
        Route::delete("{id}", [UserController::class, "delete"]);
    });
    Route::post('/logout', [AuthController::class, "logout"]);
});


Route::post('/login', [AuthController::class, "login"]);
