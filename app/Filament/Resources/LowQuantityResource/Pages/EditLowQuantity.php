<?php

namespace App\Filament\Resources\LowQuantityResource\Pages;

use App\Filament\Resources\LowQuantityResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLowQuantity extends EditRecord
{
    protected static string $resource = LowQuantityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
