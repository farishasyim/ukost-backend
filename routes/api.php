<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ComplainController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::get("room-management", [RoomController::class, "index"]);
    Route::prefix("room-management")->group(function () {
        Route::prefix("category")->group(function () {
            Route::get("{id}", [CategoryController::class, "show"]);
            Route::post("store", [CategoryController::class, "store"]);
            Route::post("{id}", [CategoryController::class, "update"]);
            Route::delete("{id}", [CategoryController::class, "delete"]);
        });
        Route::post("store", [RoomController::class, "store"]);
        Route::get("{id}", [RoomController::class, "show"]);
        Route::post("pivot-store", [RoomController::class, "storePivot"]);
        Route::post("{id}", [RoomController::class, "update"]);
        Route::delete("{id}", [RoomController::class, "delete"]);
    });
    Route::get("user-management", [UserController::class, "index"]);
    Route::prefix("user-management")->group(function () {
        Route::get("sent-credential/{id}", [UserController::class, "sentCredential"]);
        Route::post("store", [UserController::class, "store"]);
        Route::post("/{id}", [UserController::class, "update"]);
        Route::delete("{id}", [UserController::class, "delete"]);
    });
    Route::get("transaction", [TransactionController::class, "index"]);

    Route::prefix("transaction")->group(function () {
        Route::post("store", [TransactionController::class, "store"]);
        Route::get("recent", [TransactionController::class, "recentTransaction"]);
        Route::get("report", [TransactionController::class, "report"]);
        Route::get("sent-invoice/{id}", [TransactionController::class, "sentInvoice"]);
        Route::post("{id}", [TransactionController::class, "update"]);
        Route::delete("{id}", [TransactionController::class, "delete"]);
    });

    Route::get("complain", [ComplainController::class, "index"]);

    Route::prefix("complain")->group(function () {
        Route::post("store", [ComplainController::class, "store"]);
        Route::post("{id}", [ComplainController::class, "update"]);
        Route::delete("{id}", [ComplainController::class, "delete"]);
    });

    Route::get("expense", [ExpenseController::class, "index"]);

    Route::prefix("expense")->group(function () {
        Route::post("store", [ExpenseController::class, "store"]);
        Route::get("report", [ExpenseController::class, "report"]);
        Route::post("{id}", [ExpenseController::class, "update"]);
        Route::delete("{id}", [ExpenseController::class, "delete"]);
    });

    Route::post('logout', [AuthController::class, "logout"]);
    Route::post("change-password", [AuthController::class, "changePassword"]);
});

Route::post('login', [AuthController::class, "login"]);
