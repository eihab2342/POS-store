<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PurchaseGenerateResource\Pages;
use App\Models\Purchase;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\ProductVariant;
use Filament\Forms\Set;
use Illuminate\Support\Facades\Auth;

class PurchaseGenerateResource extends Resource
{
    protected static ?string $model = Purchase::class;

    protected static ?string $navigationGroup = 'عمليات الكاشير';

    protected static ?string $modelLabel = 'فاتورة شراء';

    protected static ?string $pluralModelLabel = 'فواتير شراء';
    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()?->role === 'manager';
    }

    public static function canAccess(): bool
    {
        return in_array(Auth::user()?->role, ['manager']);
    }

    public static function canViewAny(): bool
    {
        return in_array(Auth::user()?->role, ['manager']);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('بيانات المورد والفاتورة')
                ->columns(3)
                ->schema([
                    Forms\Components\Select::make('supplier_id')
                        ->label('المورد')
                        ->relationship('supplier', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),

                    Forms\Components\DateTimePicker::make('date')
                        ->label('التاريخ')
                        ->default(now())
                        ->required()
                        ->columnSpan(2),
                ]),

            Forms\Components\Section::make('بنود الفاتورة')
                ->schema([
                    Forms\Components\Repeater::make('items')
                        ->label('المنتجات')
                        ->relationship()
                        ->dehydrated() // مهم جداً لتمرير البيانات
                        ->columns(12)
                        ->minItems(1)
                        ->defaultItems(1)
                        ->live()
                        ->afterStateUpdated(function (array $state, Set $set) {
                            $total = collect($state)->sum(function ($item) {
                                $qty = (int) ($item['qty'] ?? 0);
                                $cost = (float) ($item['cost'] ?? 0);
                                return $qty * $cost;
                            });

                            $set('total_cost', $total);
                        })
                        ->schema([
                            Forms\Components\Select::make('variant_id')
                                ->label('المنتج / SKU')
                                ->relationship('variant', 'sku')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->columnSpan(6),

                            Forms\Components\TextInput::make('qty')
                                ->label('الكمية')
                                ->numeric()
                                ->default(1)
                                ->required()
                                ->live()
                                ->columnSpan(3),

                            Forms\Components\TextInput::make('cost')
                                ->label('التكلفة')
                                ->numeric()
                                ->required()
                                ->live()
                                ->columnSpan(3),
                        ])
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('إجمالي الفاتورة')
                ->columns(1)
                ->schema([
                    Forms\Components\TextInput::make('total_cost')
                        ->label('الإجمالي النهائي')
                        ->disabled()
                        ->dehydrated()
                        ->numeric()
                        ->required()
                        ->reactive(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('id')->label('#')->sortable(),
            Tables\Columns\TextColumn::make('supplier.name')->label('المورد')->searchable(),
            Tables\Columns\TextColumn::make('date')->label('التاريخ')->dateTime('Y-m-d H:i'),
            Tables\Columns\TextColumn::make('total_cost')->label('الإجمالي')->money('EGP')->sortable(),
            Tables\Columns\BadgeColumn::make('status')->label('الحالة')->colors([
                'success' => 'posted',
                'warning' => 'draft',
            ]),
        ])
            ->actions([
            // app/Filament/Resources/PurchaseResource.php (داخل table()->actions([...]))
            \Filament\Tables\Actions\Action::make('print')
                ->label('طباعة')
                ->icon('heroicon-o-printer')
                ->url(fn($record) => route('purchase.receipt.show', $record), shouldOpenInNewTab: true),
            Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPurchaseGenerates::route('/'),
            'create' => Pages\CreatePurchaseGenerate::route('/create'),
            'edit' => Pages\EditPurchaseGenerate::route('/{record}/edit'),
        ];
    }
}