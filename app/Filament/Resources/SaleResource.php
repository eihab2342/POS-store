<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SaleResource\Pages;
use App\Models\Sale;
use App\Models\ProductVariant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class SaleResource extends Resource
{
    protected static ?string $model = Sale::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $pluralModelLabel = 'المبيعات';

    public static function form(Form $form): Form
    {
        return $form->schema([
            DateTimePicker::make('date')
                ->label('تاريخ البيع')
                ->required(),

            Select::make('cashier_id')
                ->label('الكاشير')
                ->relationship('cashier', 'name')
                ->required(),

            Select::make('customer_id')
                ->label('العميل')
                ->relationship('customer', 'name')
                ->searchable()
                ->nullable(),

            Select::make('payment_method')
                ->label('طريقة الدفع')
                ->options([
                    'cash' => 'نقدًا',
                    'card' => 'بطاقة',
                    'credit' => 'ائتمان',
                ])
                ->default('cash')
                ->required(),

            Repeater::make('items')
                ->label('المنتجات')
                ->relationship()
                ->live() // علشان نحدّث الإجماليات لحظيًا
                ->afterStateUpdated(function (Set $set, Get $get) {
                    $sum = collect($get('items') ?? [])->sum(fn($i) => (float) ($i['total'] ?? 0));
                    $set('subtotal', $sum);
                    $total = $sum - (float) ($get('discount') ?? 0) + (float) ($get('tax') ?? 0);
                    $set('total', $total);
                })
                ->schema([
                    // حقل المسح بالباركود / SKU
                    TextInput::make('barcode')
                        ->label('الباركود / SKU')
                        ->placeholder('امسح الباركود هنا ثم Enter')
                        ->live(debounce: 150)
                        ->extraAttributes(['x-on:keydown.enter.prevent' => ''])
                        ->afterStateUpdated(function (Set $set, Get $get, ?string $state) {
                            if (!$state)
                                return;

                            $code = preg_replace('/[^A-Za-z0-9\-]/', '', trim($state));

                            $pv = ProductVariant::query()
                                ->where('sku', $code)
                                ->orWhere('barcode', $code)
                                ->orWhereHas('barcodes', fn($q) => $q->where('code', $code))
                                ->first();

                            if ($pv) {
                                $set('product_variant_id', $pv->id);
                                $set('price', (float) $pv->price);
                                $qty = max(1, (int) ($get('quantity') ?? 1));
                                $set('quantity', $qty);
                                $set('total', (float) $pv->price * $qty);
                            }
                        }),

                    Select::make('product_variant_id')
                        ->label('المنتج')
                        ->relationship('productVariant', 'name')
                        ->searchable()
                        ->reactive()
                        ->afterStateUpdated(function (Set $set, ?int $state) {
                            $price = optional(ProductVariant::find($state))->price ?? 0;
                            $set('price', (float) $price);
                        })
                        ->required(),

                    TextInput::make('quantity')
                        ->label('الكمية')
                        ->numeric()
                        ->default(1)
                        ->live()
                        ->afterStateUpdated(function (Set $set, Get $get, $state) {
                            $qty = (int) ($state ?: 1);
                            $price = (float) ($get('price') ?? 0);
                            $set('total', $price * $qty);
                        })
                        ->required(),

                    TextInput::make('price')
                        ->label('سعر الوحدة')
                        ->numeric()
                        ->live()
                        ->afterStateUpdated(function (Set $set, Get $get, $state) {
                            $price = (float) ($state ?: 0);
                            $qty = (int) ($get('quantity') ?? 1);
                            $set('total', $price * $qty);
                        })
                        ->required(),

                    TextInput::make('total')
                        ->label('الإجمالي')
                        ->numeric()
                        ->disabled()
                        ->dehydrated()
                        ->afterStateHydrated(function (Set $set, Get $get) {
                            $qty = (int) ($get('quantity') ?? 1);
                            $price = (float) ($get('price') ?? 0);
                            $set('total', $price * $qty);
                        }),
                ])
                ->createItemButtonLabel('إضافة منتج')
                ->columns(5),

            TextInput::make('subtotal')
                ->label('الإجمالي قبل الخصم')
                ->numeric()
                ->disabled(),

            TextInput::make('discount')
                ->label('الخصم')
                ->numeric()
                ->default(0)
                ->live()
                ->afterStateUpdated(function (Set $set, Get $get, $state) {
                    $sum = collect($get('items') ?? [])->sum(fn($i) => (float) ($i['total'] ?? 0));
                    $discount = (float) ($state ?? 0);
                    $tax = (float) ($get('tax') ?? 0);
                    $set('subtotal', $sum);
                    $set('total', $sum - $discount + $tax);
                }),

            TextInput::make('tax')
                ->label('الضريبة')
                ->numeric()
                ->default(0)
                ->live()
                ->afterStateUpdated(function (Set $set, Get $get, $state) {
                    $sum = collect($get('items') ?? [])->sum(fn($i) => (float) ($i['total'] ?? 0));
                    $discount = (float) ($get('discount') ?? 0);
                    $tax = (float) ($state ?? 0);
                    $set('subtotal', $sum);
                    $set('total', $sum - $discount + $tax);
                }),

            TextInput::make('total')
                ->label('الإجمالي النهائي')
                ->numeric()
                ->disabled(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('رقم الفاتورة'),
                TextColumn::make('customer.name')->label('العميل')->sortable()->searchable(),
                TextColumn::make('cashier.name')->label('الكاشير')->sortable()->searchable(),
                TextColumn::make('date')->label('التاريخ')->date(),
                TextColumn::make('total')->label('الإجمالي')->money('EGP')->sortable(),
                TextColumn::make('payment_method')->label('طريقة الدفع'),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // لو عندك RelationManager لعرض باركودات أو غيره، ضيفه هنا
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSales::route('/'),
            'create' => Pages\CreateSale::route('/create'),
            'edit' => Pages\EditSale::route('/{record}/edit'),
        ];
    }
}





// class SaleResource extends Resource
// {
//     protected static ?string $model = Sale::class;

//     protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
//     protected static ?string $pluralModelLabel = 'المبيعات';

//     public static function form(Form $form): Form
//     {
//         return $form->schema([
//             DateTimePicker::make('date')
//                 ->label('تاريخ البيع')
//                 ->required(),

//             Select::make('cashier_id')
//                 ->label('الكاشير')
//                 ->relationship('cashier', 'name') // لو الكاشير هو الـ User
//                 ->required(),

//             Select::make('customer_id')
//                 ->label('العميل')
//                 ->relationship('customer', 'name')
//                 ->searchable()
//                 ->nullable(),

//             Select::make('payment_method')
//                 ->label('طريقة الدفع')
//                 ->options([
//                     'cash' => 'نقدًا',
//                     'card' => 'بطاقة',
//                     'credit' => 'ائتمان',
//                 ])
//                 ->default('cash')
//                 ->required(),

//             Repeater::make('items')
//                 ->label('المنتجات')
//                 ->relationship()
//                 ->schema([
//                     Select::make('product_variant_id')
//                         ->label('المنتج')
//                         ->relationship('productVariant', 'name')
//                         ->required(),

//                     TextInput::make('quantity')
//                         ->label('الكمية')
//                         ->numeric()
//                         ->required(),

//                     TextInput::make('price')
//                         ->label('سعر الوحدة')
//                         ->numeric()
//                         ->required(),

//                     TextInput::make('total')
//                         ->label('الإجمالي')
//                         ->disabled()
//                         ->dehydrated()
//                         ->afterStateHydrated(
//                             fn($set, $get) =>
//                             $set('total', $get('quantity') * $get('price'))
//                         )
//                         ->afterStateUpdated(
//                             fn($set, $get) =>
//                             $set('total', $get('quantity') * $get('price'))
//                         ),
//                 ])
//                 ->createItemButtonLabel('إضافة منتج')
//                 ->columns(4),

//             TextInput::make('subtotal')
//                 ->label('الإجمالي قبل الخصم')
//                 ->numeric()
//                 ->disabled(),

//             TextInput::make('discount')
//                 ->label('الخصم')
//                 ->numeric()
//                 ->default(0),

//             TextInput::make('tax')
//                 ->label('الضريبة')
//                 ->numeric()
//                 ->default(0),

//             TextInput::make('total')
//                 ->label('الإجمالي النهائي')
//                 ->numeric()
//                 ->disabled(),
//         ]);
//     }

//     public static function table(Table $table): Table
//     {
//         return $table
//             ->columns([
//                 TextColumn::make('id')->label('رقم الفاتورة'),
//                 TextColumn::make('customer.name')->label('العميل')->sortable()->searchable(),
//                 TextColumn::make('cashier.name')->label('الكاشير')->sortable()->searchable(),
//                 TextColumn::make('date')->label('التاريخ')->date(),
//                 TextColumn::make('total')->label('الإجمالي')->money('EGP')->sortable(),
//                 TextColumn::make('payment_method')->label('طريقة الدفع'),
//             ])
//             ->filters([])
//             ->actions([
//                 Tables\Actions\ViewAction::make(),
//                 Tables\Actions\EditAction::make(),
//             ])
//             ->bulkActions([
//                 Tables\Actions\DeleteBulkAction::make(),
//             ]);
//     }

//     public static function getRelations(): array
//     {
//         return [
//             //
//         ];
//     }

//     public static function getPages(): array
//     {
//         return [
//             'index' => Pages\ListSales::route('/'),
//             'create' => Pages\CreateSale::route('/create'),
//             'edit' => Pages\EditSale::route('/{record}/edit'),
//         ];
//     }
// }