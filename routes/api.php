<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoomController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::get("room-management", [RoomController::class, "index"]);
});

Route::post('/login', [AuthController::class, "login"]);
