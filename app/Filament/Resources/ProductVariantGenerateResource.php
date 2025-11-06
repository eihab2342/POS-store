<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductVariantGenerateResource\Pages;
use App\Models\ProductVariant;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Tables\Actions\Action as act;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms\Components\{Select, TextInput, Toggle, Textarea, Grid};
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\Supplier;
use Illuminate\Support\Facades\Auth;

class ProductVariantGenerateResource extends Resource
{
    protected static ?string $model = ProductVariant::class;

    protected static ?int $defaultPaginationPageOption = 25;
    protected static ?string $navigationGroup = 'إدارة المخزون';
    protected static ?string $navigationIcon = 'heroicon-o-table-cells';
    protected static ?string $modelLabel = 'المنتجات';
    protected static ?string $pluralModelLabel = 'المنتجات';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('بيانات الصنف')
                    ->description('أدخل تفاصيل الصنف (المقاس، اللون، السعر...)')
                    ->schema([
                        TextInput::make('sku')
                            ->label('SKU')
                            ->disabled()
                            ->dehydrated(true)
                            ->unique(ignoreRecord: true)
                            ->prefixIcon('heroicon-o-hashtag'),

                        Forms\Components\TextInput::make('name')
                            ->label('اسم المنتج')
                            ->placeholder('مثال: تيشرت قطن من اديداس احمر'),

                        Select::make('supplier_id')
                            ->label('المورد')
                            ->relationship('supplier', 'name')->preload()
                            ->required()
                            ->suffixAction(
                                Action::make('createSupplier')
                                    ->visible(fn(Forms\Get $get) => Auth::user()?->role === 'manager')
                                    ->icon('heroicon-o-plus')
                                    ->tooltip('إضافة مورد جديد')
                                    ->action(function (array $data, Forms\Set $set) {
                                        $supplier = Supplier::create([
                                            'code' => $data['code'],
                                            'name' => $data['name'],
                                            'is_active' => $data['is_active'],
                                            'company_name' => $data['company_name'],
                                            'email' => $data['email'],
                                            'phone' => $data['phone'],
                                            'tax_number' => $data['tax_number'],
                                            'country' => $data['country'],
                                            'city' => $data['city'],
                                            'address' => $data['address'],
                                            'opening_balance' => $data['opening_balance'],
                                            'notes' => $data['notes'],
                                        ]);
                                        // امسح كاش خيارات الموردين بالمفتاح الصحيح
                                        Cache::forget('suppliers:options');
                                        $set('supplier_id', $supplier->id);
                                    })
                                    ->form([
                                        Grid::make(12)->schema([
                                            TextInput::make('code')
                                                ->label('كود المورد')
                                                ->required()
                                                ->unique(table: \App\Models\Supplier::class, column: 'code', ignoreRecord: true)
                                                ->maxLength(30)
                                                ->columnSpan(3),

                                            TextInput::make('name')
                                                ->label('الاسم')
                                                ->required()
                                                ->maxLength(150)
                                                ->columnSpan(6),

                                            Toggle::make('is_active')
                                                ->label('نشِط؟')
                                                ->default(true)
                                                ->columnSpan(3),
                                        ]),

                                        Grid::make(12)->schema([
                                            TextInput::make('company_name')
                                                ->label('الشركة')
                                                ->columnSpan(4),

                                            TextInput::make('email')
                                                ->label('البريد الإلكتروني')
                                                ->email()
                                                ->unique(table: \App\Models\Supplier::class, column: 'email', ignoreRecord: true)
                                                ->nullable()
                                                ->columnSpan(4),

                                            TextInput::make('phone')
                                                ->label('الهاتف')
                                                ->tel()
                                                ->regex('/^[0-9+\-\s()]{6,20}$/')
                                                ->columnSpan(4),
                                        ]),

                                        Grid::make(12)->schema([
                                            TextInput::make('tax_number')
                                                ->label('الرقم الضريبي')
                                                ->unique(table: \App\Models\Supplier::class, column: 'tax_number', ignoreRecord: true)
                                                ->nullable()
                                                ->columnSpan(4),

                                            TextInput::make('country')
                                                ->label('الدولة')
                                                ->columnSpan(4),

                                            TextInput::make('city')
                                                ->label('المدينة')
                                                ->columnSpan(4),
                                        ]),

                                        TextInput::make('address')
                                            ->label('العنوان')
                                            ->maxLength(255),

                                        Grid::make(12)->schema([
                                            TextInput::make('opening_balance')
                                                ->label('رصيد افتتاحي')
                                                ->numeric()
                                                ->default(0)
                                                ->minValue(0)
                                                ->columnSpan(6)
                                                ->helperText('يستخدم لمرة واحدة، للتأسيس'),

                                            TextInput::make('current_balance')
                                                ->label('الرصيد الحالي')
                                                ->numeric()
                                                ->default(0)
                                                ->minValue(0)
                                                ->dehydrated(false)
                                                ->disabled()
                                                ->columnSpan(6)
                                                ->helperText('يُحدَّث آليًا من المشتريات والمدفوعات'),
                                        ]),

                                        Textarea::make('notes')
                                            ->label('ملاحظات')
                                            ->rows(3),
                                    ])
                                    ->modalHeading('إضافة مورد جديد')
                                    ->modalSubmitActionLabel('حفظ')
                            ),

                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('color')
                                ->label('اللون')
                                ->placeholder('مثال: أحمر')
                                ->maxLength(50),

                            Forms\Components\TextInput::make('size')
                                ->label('المقاس')
                                ->placeholder('مثال: M / L / XL')
                                ->maxLength(10),
                        ]),

                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('cost')
                                ->label('تكلفة الشراء')
                                ->numeric()
                                ->prefix('ج.م')
                                ->required(),

                            Forms\Components\TextInput::make('price')
                                ->label('سعر البيع')
                                ->numeric()
                                ->prefix('ج.م')
                                ->required(),
                        ]),

                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('stock_qty')
                                ->label('المخزون الحالي')
                                ->numeric()
                                ->default(0),

                            Forms\Components\TextInput::make('reorder_level')
                                ->label('حد إعادة الطلب')
                                ->numeric()
                                ->default(0)
                                ->hint('سيظهر تنبيه عند انخفاض الكمية لهذا الحد'),
                        ]),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->deferLoading()
            ->columns([
                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('المنتج')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('size')
                    ->label('المقاس')
                    ->sortable()
                    ->badge(),

                Tables\Columns\TextColumn::make('color')
                    ->label('اللون')
                    ->sortable()
                    ->badge(),

                Tables\Columns\TextColumn::make('price')
                    ->label('السعر')
                    ->money('EGP'),

                Tables\Columns\TextColumn::make('stock_qty')
                    ->label('المخزون')
                    ->sortable()
                    ->badge()
                    ->color(fn($state) => $state <= 0 ? 'danger' : ($state < 5 ? 'warning' : 'success')),
            ])
            ->filters([
                Filter::make('low_stock')
                    ->label('مخزون منخفض')
                    ->query(fn(Builder $q) => $q->where('is_low_stock', true)),

                SelectFilter::make('supplier_id')
                    ->label('المورد')
                    ->options(fn() => Cache::remember(
                        'suppliers:options',
                        3600,
                        fn() => Supplier::orderBy('name')->pluck('name', 'id')->toArray()
                    )),
            ])
            ->actions([
                Tables\Actions\Action::make('labels')
                    ->label('طباعة استيكرات')
                    ->icon('heroicon-o-printer')
                    ->form([
                        Forms\Components\TextInput::make('qty')
                            ->numeric()
                            ->default(100)
                            ->minValue(1)
                            ->maxValue(2000)
                            ->required(),
                        Forms\Components\Select::make('type')
                            ->options([
                                'code128' => 'Code128',
                                'qr' => 'QR',
                                'ean13' => 'EAN-13',
                            ])
                            ->default('code128'),
                    ])
                    ->url(fn($record, $data) => route('barcodes.print', [
                        'variant' => $record->id,
                        'qty' => $data['qty'] ?? 100,
                        'type' => $data['type'] ?? 'code128',
                    ]))
                    ->openUrlInNewTab(),

                Tables\Actions\ViewAction::make(),

                Tables\Actions\EditAction::make()
                    ->visible(fn() => Auth::user()?->role === 'manager')
                    ->after(fn() => Cache::forget('suppliers:options')),

                Tables\Actions\DeleteAction::make()
                    ->visible(fn() => Auth::user()?->role === 'manager')
                    ->after(fn() => Cache::forget('suppliers:options')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->after(fn() => Cache::forget('suppliers:options')),
                ]),
            ])
            ->persistFiltersInSession()
            ->persistSearchInSession()
            ->persistColumnSearchesInSession();
    }

    public static function getEloquentQuery(): Builder
    {
        Log::info('🔍 Building Product Variants Query', [
            'time' => now()->format('Y-m-d H:i:s'),
        ]);

        return parent::getEloquentQuery()
            ->with('supplier:id,name')
            ->select([
                'id',
                'name',
                'sku',
                'product_id',
                'supplier_id',
                'size',
                'color',
                'price',
                'stock_qty',
                'reorder_level',
            ])
            ->orderByDesc('id');
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
            'index' => Pages\ListProductVariantGenerates::route('/'),
            'create' => Pages\CreateProductVariantGenerate::route('/create'),
            'edit' => Pages\EditProductVariantGenerate::route('/{record}/edit'),
        ];
    }
}
