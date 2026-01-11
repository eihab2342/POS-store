<?php

namespace App\Providers;

use App\Models\ProductVariant;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use App\Observers\ProductVariantObserver;
use App\Observers\SupplierObserver;
use App\Observers\SupplierTransactionObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Vite;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Supplier::observe(SupplierObserver::class);
        // SupplierPurchase::observe(SupplierTransactionObserver::class);
        SupplierPayment::observe(SupplierTransactionObserver::class);
        ProductVariant::observe(ProductVariantObserver::class);

        // فحص ما إذا كنا نستخدم ngrok أو رابط خارجي آمن
        if (env('APP_ENV') !== 'local' || str_contains(env('APP_URL'), 'https://')) {
            URL::forceScheme('https');
        }

    }
}
