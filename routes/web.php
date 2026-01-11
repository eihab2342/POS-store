<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BalanceController;
use App\Http\Controllers\BarcodeController;
use App\Http\Controllers\CreditController;
use App\Http\Controllers\CustomerGenerateController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\LowQuantityController;
use App\Http\Controllers\ProductVariantController;
use App\Http\Controllers\ProfitController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SaleReturnController;
use App\Http\Controllers\SalesInvoiceController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SupplierPaymentController;
use App\Models\Sale;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {

    // مشترك بين manager و admin
    Route::get('/', fn () => redirect()->route('dashboard'))->name('home');
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard')
        ->middleware('role:manager,admin');

    /**
     * ======================
     *  Routes للـ MANAGER فقط
     * ======================
     */
    Route::middleware('role:manager')->group(function () {

        // Admin Routes
        Route::prefix('admins')->name('admins.')->group(function () {
            Route::resource('/', AdminController::class)->parameters(['' => 'admin']);
            Route::delete('/bulk-destroy', [AdminController::class, 'bulkDestroy'])
                ->name('bulk-destroy');
        });

        // Balance Routes
        Route::prefix('balances')->name('balances.')->group(function () {
            Route::resource('/', BalanceController::class)->parameters(['' => 'balance']);
        });

        // Customer Generate Routes
        Route::prefix('customers-generate')->name('customers.')->group(function () {
            Route::resource('/', CustomerGenerateController::class)->parameters(['' => 'customer']);
        });

        // Low Quantity Routes
        Route::prefix('low-quantity')->name('low.')->group(function () {
            Route::resource('/', LowQuantityController::class)->parameters(['' => 'low']);
        });
        // routes/web.php
        // ... routes أخرى ...

        // مسارات المصروفات
        Route::prefix('expenses')->name('expenses.')->group(function () {
            Route::get('/', [ExpenseController::class, 'index'])->name('index');
            Route::get('/create', [ExpenseController::class, 'create'])->name('create');
            Route::post('/', [ExpenseController::class, 'store'])->name('store');
            Route::get('/{expense}', [ExpenseController::class, 'show'])->name('show');
            Route::get('/{expense}/download', [ExpenseController::class, 'downloadAttachment'])->name('download');

            // الموافقة والرفض
            Route::post('/{expense}/approve', [ExpenseController::class, 'approve'])->name('approve');
            Route::post('/{expense}/reject', [ExpenseController::class, 'reject'])->name('reject');
            Route::post('/{expense}/paid', [ExpenseController::class, 'markAsPaid'])->name('markPaid');

            // التقارير
            Route::get('/report', [ExpenseController::class, 'report'])->name('report');
        });
        // Product Variant Routes
        Route::prefix('products')->name('variants.')->group(function () {
            Route::get('/', [ProductVariantController::class, 'index'])->name('index');
            Route::get('/create', [ProductVariantController::class, 'create'])->name('create');
            Route::post('/', [ProductVariantController::class, 'store'])->name('store');
            Route::get('/{variant}/edit', [ProductVariantController::class, 'edit'])->name('edit');
            Route::put('/{variant}', [ProductVariantController::class, 'update'])->name('update');
            Route::delete('/{variant}', [ProductVariantController::class, 'destroy'])->name('destroy');

            Route::get('/{variant}/print-labels', [ProductVariantController::class, 'printLabels'])->name('print.labels');
            Route::post('/print-inventory', [ProductVariantController::class, 'printInventory'])->name('print.inventory');
            Route::get('/bulk-edit', [ProductVariantController::class, 'bulkEdit'])->name('bulk.edit');
            Route::post('/bulk-update', [ProductVariantController::class, 'bulkUpdate'])->name('bulk.update');
        });

        // Live Search and Barcode Routes
        Route::get('/variants/live-search', [ProductVariantController::class, 'liveSearch'])->name('variants.liveSearch');
        Route::get('/barcodes/print/{variant}', [BarcodeController::class, 'preview'])->name('barcodes.print');

        // Profit Routes
        Route::prefix('profits')->name('profits.')->group(function () {
            Route::get('/', [ProfitController::class, 'index'])->name('index');
            Route::get('/{sale}', [ProfitController::class, 'show'])->name('show');
        });

        // Supplier Routes
        Route::resource('suppliers', SupplierController::class);
        Route::post('suppliers/{supplier}/recalculate', [SupplierController::class, 'recalculateBalance'])->name('suppliers.recalculate');

        // Purchase Routes
        Route::get('suppliers/{supplier}/purchases/create', [PurchaseController::class, 'create'])->name('purchases.create');
        Route::post('purchases', [PurchaseController::class, 'store'])->name('purchases.store');
        Route::get('purchases/{purchase}/edit', [PurchaseController::class, 'edit'])->name('purchases.edit');
        Route::put('purchases/{purchase}', [PurchaseController::class, 'update'])->name('purchases.update');
        Route::delete('purchases/{purchase}', [PurchaseController::class, 'destroy'])->name('purchases.destroy');

        // Supplier Payment Routes
        Route::get('suppliers/{supplier}/payments/create', [SupplierPaymentController::class, 'create'])->name('payments.create');
        Route::post('payments', [SupplierPaymentController::class, 'store'])->name('payments.store');
        Route::get('payments/{payment}/edit', [SupplierPaymentController::class, 'edit'])->name('payments.edit');
        Route::put('payments/{payment}', [SupplierPaymentController::class, 'update'])->name('payments.update');
        Route::delete('payments/{payment}', [SupplierPaymentController::class, 'destroy'])->name('payments.destroy');

        // Profit Report Route
        Route::get('/profits-report', [ProfitController::class, 'printReport'])->name('profit.print-report');
    });

    /**
     * ==========================
     *  Routes مشتركة (manager + admin)
     * ==========================
     */
    Route::prefix('credits')->name('credits.')->middleware('role:manager,admin')->group(function () {
        Route::get('/', [CreditController::class, 'index'])->name('index');
        Route::get('/{credit}', [CreditController::class, 'show'])->name('show');
        Route::post('/{credit}/pay', [CreditController::class, 'pay'])->name('pay');
    });
    // Sales Return Routes
    Route::resource('returns', SaleReturnController::class);
    Route::get('/returns/sale-details/{id}', [SaleReturnController::class, 'getSaleDetails'])->name('returns.sale.details');

    // Sales Invoice Routes
    Route::prefix('sales')->name('sales.')->group(function () {
        Route::get('/invoice', [SalesInvoiceController::class, 'show'])->name('invoice')->middleware('role:manager,admin');

        // Ajax routes
        Route::get('/invoice/cart', [SalesInvoiceController::class, 'getCart'])->name('invoice.cart')->middleware('role:manager,admin');
        Route::post('/invoice/add', [SalesInvoiceController::class, 'addItem'])->name('invoice.add')->middleware('role:manager,admin');
        Route::post('/invoice/update', [SalesInvoiceController::class, 'updateItem'])->name('invoice.update')->middleware('role:manager,admin');
        Route::post('/invoice/remove', [SalesInvoiceController::class, 'removeItem'])->name('invoice.remove')->middleware('role:manager,admin');
        Route::post('/invoice/reset', [SalesInvoiceController::class, 'resetCart'])->name('invoice.reset')->middleware('role:manager,admin');
        Route::post('/invoice/checkout', [SalesInvoiceController::class, 'checkout'])->name('invoice.checkout')->middleware('role:manager,admin');
        Route::middleware('role:manager')->group(function () {
            Route::get('/', [SaleController::class, 'index'])->name('index');
            Route::get('/create', [SaleController::class, 'create'])->name('create');
            Route::post('/', [SaleController::class, 'store'])->name('store');
            Route::get('/{sale}', [SaleController::class, 'show'])->name('show');
            Route::get('/{sale}/edit', [SaleController::class, 'edit'])->name('edit');
            Route::put('/{sale}', [SaleController::class, 'update'])->name('update');
            Route::delete('/{sale}', [SaleController::class, 'destroy'])->name('destroy');
            Route::post('/bulk-delete', [SaleController::class, 'bulkDelete'])->name('bulk-delete');
        });
        // main
        Route::post('/invoice/add-ajax', [SalesInvoiceController::class, 'addItemAjax'])
            ->name('invoice.addAjax');

    });

    Route::get('/sales/{sale}/receipt', function (Sale $sale) {
        $sale->load('items.productVariant', 'customer');

        return view('receipt', compact('sale'));
    })->name('receipt.show')->middleware('role:manager,admin');

    Route::middleware(['throttle:10,1'])
        ->prefix('system-tools')
        ->name('system-tools.')
        ->group(function () {

            Route::get('/cache/clear', function () {
                Artisan::call('cache:clear');
                Artisan::call('config:clear');
                Artisan::call('route:clear');
                Artisan::call('view:clear');

                return back()->with('success', 'تم مسح الكاش بالكامل ✅');
            })->name('cache.clear');

            Route::get('/cache/build', function () {
                Artisan::call('config:cache');
                Artisan::call('route:cache');
                Artisan::call('view:cache');

                return back()->with('success', 'تم إعادة بناء الكاش ✅');
            })->name('cache.build');

            Route::get('/optimize', function () {
                Artisan::call('optimize');

                return back()->with('success', 'تم تنفيذ Optimize ✅');
            })->name('optimize');

            Route::get('/optimize/clear', function () {
                Artisan::call('optimize:clear');

                return back()->with('success', 'تم تنفيذ Optimize Clear ✅');
            })->name('optimize.clear');

        });
});

// Logout Route
Route::controller(AuthController::class)->group(function () {
    // Login Routes
    Route::get('/login', 'showLoginForm')->name('login')->middleware('guest');
    Route::post('/login', 'login')->name('login.attempt')->middleware('guest');

    // Logout Route
    Route::post('/logout', 'logout')->name('logout')->middleware('auth');
});
