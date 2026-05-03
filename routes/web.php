<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController;

Route::get('/', [TransactionController::class, 'index'])->name('dashboard');
Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
Route::put('/transactions/{transaction}', [TransactionController::class, 'update'])->name('transactions.update');
Route::delete('/transactions/{transaction}', [TransactionController::class, 'destroy'])->name('transactions.destroy');
Route::get('/report', [TransactionController::class, 'report'])->name('report');

use App\Http\Controllers\RabController;
Route::get('/rab', [RabController::class, 'index'])->name('rab.index');
Route::post('/rab', [RabController::class, 'store'])->name('rab.store');
Route::delete('/rab/{rab}', [RabController::class, 'destroy'])->name('rab.destroy');
Route::get('/rab/{rab}/report', [RabController::class, 'report'])->name('rab.report');
