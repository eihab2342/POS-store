<?php

namespace App\Filament\Resources\ProductGenerateResource\Pages;

use App\Filament\Resources\ProductGenerateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProductGenerate extends EditRecord
{
    protected static string $resource = ProductGenerateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
