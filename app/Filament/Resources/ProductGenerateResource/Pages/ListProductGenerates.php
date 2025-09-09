<?php

namespace App\Filament\Resources\ProductGenerateResource\Pages;

use App\Filament\Resources\ProductGenerateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProductGenerates extends ListRecords
{
    protected static string $resource = ProductGenerateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
