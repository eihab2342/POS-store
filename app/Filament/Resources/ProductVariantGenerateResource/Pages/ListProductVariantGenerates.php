<?php

namespace App\Filament\Resources\ProductVariantGenerateResource\Pages;

use App\Filament\Resources\ProductVariantGenerateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProductVariantGenerates extends ListRecords
{
    protected static string $resource = ProductVariantGenerateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
