<?php

namespace App\Filament\Resources\SupplierGenerateResource\Pages;

use App\Filament\Resources\SupplierGenerateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSupplierGenerates extends ListRecords
{
    protected static string $resource = SupplierGenerateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
