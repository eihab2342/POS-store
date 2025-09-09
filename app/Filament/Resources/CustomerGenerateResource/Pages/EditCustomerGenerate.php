<?php

namespace App\Filament\Resources\CustomerGenerateResource\Pages;

use App\Filament\Resources\CustomerGenerateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCustomerGenerate extends EditRecord
{
    protected static string $resource = CustomerGenerateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
