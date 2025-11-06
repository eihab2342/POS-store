<?php

namespace App\Filament\Resources\SaleResource\Pages;

use App\Filament\Resources\SaleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSales extends ListRecords
{
    protected static string $resource = SaleResource::class;


    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->url(route('filament.admin.pages.sales-invoice'))
                ->label('إنشاء فاتورة') 
                ->icon('heroicon-o-plus')
                ->color('success'),
        ];
    }
}