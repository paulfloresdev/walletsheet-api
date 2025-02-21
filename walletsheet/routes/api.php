<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SheetController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    // Rutas de AccountController (requieren autenticación)
    Route::apiResource('accounts', AccountController::class);
    Route::get('/accounts/filter/{filter}', [AccountController::class, 'filterByType']);

    Route::apiResource('categories', CategoryController::class);

    Route::apiResource('transactions', TransactionController::class);

    // Ruta para obtener los meses con transacciones realizadas por el usuario
    Route::get('/transaction-months', [SheetController::class, 'getTransactionMonths']);

    // Ruta para obtener las transacciones de un mes específico con sumatorias
    Route::get('/transactions/{month}/{year}', [SheetController::class, 'getTransactionsByMonth']);

    // Ruta para obtener el saldo inicial y final de las cuentas de débito hasta el último día del mes anterior
    Route::get('/balances/{month}/{year}', [SheetController::class, 'getBalanceForMonth']);

    Route::get('/month-data/{month}/{year}', [SheetController::class, 'getMonthData']);

});
