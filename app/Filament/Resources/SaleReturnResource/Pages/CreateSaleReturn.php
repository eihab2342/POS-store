<?php

namespace App\Filament\Resources\SaleReturnResource\Pages;

use App\Filament\Resources\SaleReturnResource;
use App\Models\SaleReturn;
use Filament\Resources\Pages\CreateRecord;

class CreateSaleReturn extends CreateRecord
{
    protected static string $resource = SaleReturnResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // هندلها في create بدال ما ترجع خطأ
        return $data;
    }

    protected function handleRecordCreation(array $data): SaleReturn
    {
        // اتأكد ان فيه عناصر مرتجعة
        $items = $data['items'] ?? [];

        foreach ($items as $item) {
            if (($item['return_qty'] ?? 0) > 0) {
                SaleReturn::create([
                    'sale_id' => $data['sale_id'],
                    'product_variant_id' => $item['product_variant_id'],
                    'returned_qty' => $item['return_qty'],
                    'reason' => $data['reason'] ?? null,
                    'user_id' => $data['user_id'],
                ]);
            }
        }

        // ممكن ترجع أول سجل لو عايز ترجع نجاح
        return new SaleReturn();
    }
}
