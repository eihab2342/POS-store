<?php

use App\Http\Controllers\BarcodeController;
use App\Http\Controllers\POSController;
use App\Http\Controllers\PurchasePrintController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return redirect()->route('filament.admin.pages.dashboard');
});
Route::get('/pos', [POSController::class, 'index'])->name('pos.index');
Route::post('/pos/scan', [POSController::class, 'scan'])->name('pos.scan');
Route::post('/pos/checkout', [POSController::class, 'checkout']);
// Route::get('/receipt/{sale}', [POSController::class, 'receipt'])->name('receipt.show');
Route::get('/purchase/receipt/{purchase}', [PurchasePrintController::class, 'show'])
    ->name('purchase.receipt.show');
// routes/web.php
Route::get('/sales/{sale}/receipt', function (\App\Models\Sale $sale) {
    $sale->load('items.product', 'customer');
    return view('sales.receipt', compact('sale'));
})->name('receipt.show');
Route::get('/print/sale/{sale}', [PurchasePrintController::class, 'printSale'])
    ->name('sale.print');

// routes/web.php
Route::get('/barcodes/print/{variant}', [BarcodeController::class, 'print'])->name('barcodes.print');
