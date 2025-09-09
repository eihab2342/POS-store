<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SaleResource\Pages;
use App\Filament\Resources\SaleResource\RelationManagers;
use App\Models\Sale;
use Closure;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
                ->relationship('cashier', 'name') // لو الكاشير هو الـ User
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
                ->schema([
                    Select::make('product_variant_id')
                        ->label('المنتج')
                        ->relationship('productVariant', 'name')
                        ->required(),

                    TextInput::make('quantity')
                        ->label('الكمية')
                        ->numeric()
                        ->required(),

                    TextInput::make('price')
                        ->label('سعر الوحدة')
                        ->numeric()
                        ->required(),

                    TextInput::make('total')
                        ->label('الإجمالي')
                        ->disabled()
                        ->dehydrated()
                        ->afterStateHydrated(
                            fn($set, $get) =>
                            $set('total', $get('quantity') * $get('price'))
                        )
                        ->afterStateUpdated(
                            fn($set, $get) =>
                            $set('total', $get('quantity') * $get('price'))
                        ),
                ])
                ->createItemButtonLabel('إضافة منتج')
                ->columns(4),

            TextInput::make('subtotal')
                ->label('الإجمالي قبل الخصم')
                ->numeric()
                ->disabled(),

            TextInput::make('discount')
                ->label('الخصم')
                ->numeric()
                ->default(0),

            TextInput::make('tax')
                ->label('الضريبة')
                ->numeric()
                ->default(0),

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
            //
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