<?php

namespace App\Filament\Resources\PurchaseGenerateResource\Pages;

use App\Filament\Resources\PurchaseGenerateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPurchaseGenerates extends ListRecords
{
    protected static string $resource = PurchaseGenerateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}