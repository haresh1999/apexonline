<?php

use App\Http\Controllers\Admin\BankController;
use App\Http\Controllers\Admin\PayoutController;
use Illuminate\Support\Facades\Route;

Route::get('bank/list', [BankController::class, 'apiList'])->middleware('auth');
Route::post('bank/create', [BankController::class, 'apiCreate'])->middleware('auth');
Route::post('payout/request', [PayoutController::class, 'apiCreate'])->middleware('auth');
