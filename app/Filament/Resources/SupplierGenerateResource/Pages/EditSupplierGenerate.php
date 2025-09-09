<?php

namespace App\Filament\Resources\SupplierGenerateResource\Pages;

use App\Filament\Resources\SupplierGenerateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSupplierGenerate extends EditRecord
{
    protected static string $resource = SupplierGenerateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
