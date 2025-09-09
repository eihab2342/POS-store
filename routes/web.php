<?php

use App\Http\Controllers\POSController;
use App\Http\Controllers\PurchasePrintController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return redirect()->route('filament.admin.pages.dashboard');
});
Route::get('/pos', [POSController::class, 'index'])->name('pos.index');
Route::post('/pos/scan', [POSController::class, 'scan'])->name('pos.scan');
Route::post('/pos/checkout', [POSController::class, 'checkout']);
Route::get('/receipt/{sale}', [POSController::class, 'receipt'])->name('receipt.show');
Route::get('/purchase/receipt/{purchase}', [PurchasePrintController::class, 'show'])
    ->name('purchase.receipt.show');