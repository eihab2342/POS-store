<?php

namespace App\Filament\Resources\ProductVariantGenerateResource\Pages;

use App\Filament\Resources\ProductVariantGenerateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProductVariantGenerate extends EditRecord
{
    protected static string $resource = ProductVariantGenerateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        if ($this->record->wasChanged('stock_qty')) {
            \App\Services\BarcodeService::syncToStock($this->record);
            \App\Services\AutoBarcodeService::generateAndPrint($this->record);
        }
    }
}
