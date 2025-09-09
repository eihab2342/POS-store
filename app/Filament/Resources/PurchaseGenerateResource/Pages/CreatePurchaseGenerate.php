<?php

namespace App\Filament\Resources\PurchaseGenerateResource\Pages;

use App\Filament\Resources\PurchaseGenerateResource;
use Filament\Resources\Pages\CreateRecord;
use App\Services\PurchaseService;
use App\Models\Purchase;

class CreatePurchaseGenerate extends CreateRecord
{
    protected static string $resource = PurchaseGenerateResource::class;

    protected function handleRecordCreation(array $data): Purchase
    {
        $purchaseId = app(PurchaseService::class)->create(
            supplierId: $data['supplier_id'],
            lines: $data['items'] ?? [],
        );

        return Purchase::findOrFail($purchaseId);
    }
}