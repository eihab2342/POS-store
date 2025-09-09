<?php

namespace App\Providers;

use App\Interfaces\ProductVarientRepoInterface;
use App\Repositories\ProductVariantRepository;
use App\Repositories\SaleRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ProductVarientRepoInterface::class, ProductVariantRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}