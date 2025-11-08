<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SaleReturnResource\Pages;
use App\Models\SaleReturn;
use App\Models\SaleItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SaleReturnResource extends Resource
{
    protected static ?string $model = SaleReturn::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-uturn-left';
    protected static ?string $modelLabel = 'مرتجع';
    protected static ?string $pluralModelLabel = 'المرتجعات';
    protected static ?string $navigationGroup = 'إدارة المبيعات';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('معلومات المرتجع')
                ->schema([
                    Forms\Components\Select::make('sale_id')
                        ->label('رقم الفاتورة')
                        ->searchable()
                        ->live(onBlur: true)
                        ->preload()
                        ->getSearchResultsUsing(function (string $search) {
                            return \App\Models\Sale::where('id', 'like', "%{$search}%")
                                ->limit(10)
                                ->pluck('id', 'id');
                        })
                        ->getOptionLabelUsing(fn($value) => "فاتورة رقم {$value}")
                        ->afterStateUpdated(function ($state, callable $set) {
                            if (!$state) {
                                $set('items', []);
                                return;
                            }

                            $items = SaleItem::where('sale_id', $state)
                                ->with('variant:id,name')
                                ->get()
                                ->map(function ($item) {
                                    return [
                                        'product_variant_id' => $item->product_variant_id,
                                        'variant_name' => $item->variant->name,
                                        'sold_qty' => $item->qty,
                                        'return_qty' => $item->qty,
                                    ];
                                })
                                ->toArray();

                            $set('items', $items);
                        })
                        ->required()
                        ->columnSpanFull(),
                    Forms\Components\Section::make('المنتجات المرتجعة')
                        ->schema([
                            Forms\Components\Placeholder::make('no_sale_selected')
                                ->label('')
                                ->content('⚠️ الرجاء اختيار رقم الفاتورة أولاً لعرض المنتجات')
                                ->visible(fn(callable $get) => !$get('sale_id')),

                            Forms\Components\Repeater::make('items')
                                ->label('')
                                ->schema([
                                    Forms\Components\Hidden::make('product_variant_id'),

                                    Forms\Components\TextInput::make('variant_name')
                                        ->label('اسم المنتج')
                                        ->disabled()
                                        ->dehydrated(false)
                                        ->columnSpan(2),

                                    Forms\Components\TextInput::make('sold_qty')
                                        ->label('الكمية المباعة')
                                        ->disabled()
                                        ->dehydrated(false)
                                        ->suffix('وحدة')
                                        ->columnSpan(1),

                                    Forms\Components\TextInput::make('return_qty')
                                        ->label('الكمية المرتجعة')
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue(fn(callable $get) => $get('sold_qty') ?? 0)
                                        ->default(fn(callable $get) => $get('sold_qty') ?? 0)
                                        ->suffix('وحدة')
                                        ->required()
                                        ->live()
                                        ->columnSpan(1),
                                ])
                                ->columns(4)
                                ->defaultItems(0)
                                ->addable(false)
                                ->deletable(false)
                                ->reorderable(false)
                                ->visible(fn(callable $get) => $get('sale_id') && !empty($get('items')))
                                ->columnSpanFull(),
                        ])
                        ->collapsible()
                        ->collapsed(fn(callable $get) => !$get('sale_id')),
                    Forms\Components\Textarea::make('reason')
                        ->label('سبب الإرجاع')
                        ->rows(3)
                        ->maxLength(255)
                        ->columnSpanFull(),

                    Forms\Components\Select::make('user_id')
                        ->label('المستخدم')
                        ->relationship('user', 'name')
                        ->preload()
                        ->default(auth()->id())
                        ->required()
                        ->columnSpanFull(),
                ])
                ->columns(1),

            // Forms\Components\Section::make('المنتجات المرتجعة')
            //     ->schema([
            //         Forms\Components\Placeholder::make('no_sale_selected')
            //             ->label('')
            //             ->content('⚠️ الرجاء اختيار رقم الفاتورة أولاً لعرض المنتجات')
            //             ->visible(fn(callable $get) => !$get('sale_id')),

            //         Forms\Components\Repeater::make('items')
            //             ->label('')
            //             ->schema([
            //                 Forms\Components\Hidden::make('product_variant_id'),

            //                 Forms\Components\TextInput::make('variant_name')
            //                     ->label('اسم المنتج')
            //                     ->disabled()
            //                     ->dehydrated(false)
            //                     ->columnSpan(2),

            //                 Forms\Components\TextInput::make('sold_qty')
            //                     ->label('الكمية المباعة')
            //                     ->disabled()
            //                     ->dehydrated(false)
            //                     ->suffix('وحدة')
            //                     ->columnSpan(1),

            //                 Forms\Components\TextInput::make('return_qty')
            //                     ->label('الكمية المرتجعة')
            //                     ->numeric()
            //                     ->minValue(0)
            //                     ->maxValue(fn(callable $get) => $get('sold_qty') ?? 0)
            //                     ->default(fn(callable $get) => $get('sold_qty') ?? 0)
            //                     ->suffix('وحدة')
            //                     ->required()
            //                     ->live()
            //                     ->columnSpan(1),
            //             ])
            //             ->columns(4)
            //             ->defaultItems(0)
            //             ->addable(false)
            //             ->deletable(false)
            //             ->reorderable(false)
            //             ->visible(fn(callable $get) => $get('sale_id') && !empty($get('items')))
            //             ->columnSpanFull(),
            //     ])
            //     ->collapsible()
            //     ->collapsed(fn(callable $get) => !$get('sale_id')),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sale.id')
                    ->label('رقم الفاتورة')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('variant.name')
                    ->label('المنتج')
                    ->searchable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('returned_qty')
                    ->label('الكمية')
                    ->badge()
                    ->color('warning')
                    ->suffix(' وحدة'),

                Tables\Columns\TextColumn::make('reason')
                    ->label('السبب')
                    ->limit(30)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('بواسطة')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('التاريخ')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('sale_id')
                    ->label('رقم الفاتورة')
                    ->searchable(),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('من تاريخ'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('إلى تاريخ'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['sale:id', 'variant:id,name', 'user:id,name'])
            ->select(['id', 'sale_id', 'product_variant_id', 'returned_qty', 'reason', 'user_id', 'created_at']);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSaleReturns::route('/'),
            'create' => Pages\CreateSaleReturn::route('/create'),
            'edit' => Pages\EditSaleReturn::route('/{record}/edit'),
        ];
    }
}