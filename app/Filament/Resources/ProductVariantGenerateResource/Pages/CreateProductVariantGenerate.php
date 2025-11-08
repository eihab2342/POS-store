<?php

namespace App\Filament\Resources\ProductVariantGenerateResource\Pages;

use App\Filament\Resources\ProductVariantGenerateResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redirect;

class CreateProductVariantGenerate extends CreateRecord
{
    protected static string $resource = ProductVariantGenerateResource::class;

    protected function afterCreate(): void
    {

        // أولاً نضمن توليد الباركودات الناقصة
        \App\Services\BarcodeService::syncToStock($this->record);

        // ثم نبعث للطابعة مباشرة (هيطبع فقط اللي اتولد جديد)
        \App\Services\AutoBarcodeService::generateAndPrint($this->record);
        // Cache::tags(['product_variants', 'suppliers'])->flush();

    }
}
