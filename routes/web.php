<?php

use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return [
        "status" => false,
    ];
});

Route::get("income-report", [TransactionController::class, "reportView"]);
Route::get("expense-report", [ExpenseController::class, "reportView"]);
