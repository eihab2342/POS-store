<?php

namespace App\Filament\Resources\PurchaseGenerateResource\Pages;

use App\Filament\Resources\PurchaseGenerateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPurchaseGenerate extends EditRecord
{
    protected static string $resource = PurchaseGenerateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
