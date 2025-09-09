<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\ProductVariant;
use App\Models\Sale;
use App\Models\Supplier;
use Illuminate\Support\Facades\Cache;

class StatsOverview extends BaseWidget
{
    public function getStats(): array
    {
        $productCount = Cache::remember('stats.product_variant_count', now()->addMinutes(10), function () {
            return ProductVariant::count();
        });
        
        $salesCount = Cache::remember('stats.sales_count', now()->addMinutes(10), function () {
            return Sale::count();
        });

        $supplierCount = Cache::remember('stats.supplier_count', now()->addMinutes(10), function () {
            return Supplier::count();
        });

        $lowStockCount = Cache::remember('stats.low_stock_count', now()->addMinutes(10), function () {
            return ProductVariant::whereColumn('stock_qty', '<', 'reorder_level')->count();
        });

        return [
            Stat::make('عدد المبيعات', $salesCount)
                ->description('إجمالي المبيعات')
                ->color('primary')
                ->url(route('filament.admin.resources.product-variant-generates.index')),

            Stat::make('عدد المنتجات', $productCount)
                ->description('إجمالي المنتجات')
                ->color('primary')
                ->url(route('filament.admin.resources.product-variant-generates.index')),

            Stat::make('عدد الموردين', $supplierCount)
                ->description('إجمالي الموردين')
                ->color('success')
                ->url(route('filament.admin.resources.supplier-generates.index')),

            Stat::make('مخزون منخفض', $lowStockCount)
                ->description('منتجات تحتاج إعادة طلب')
                ->color('danger')
                ->url(route('filament.admin.resources.low-quantities.index')),
        ];
    }
}