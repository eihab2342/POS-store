<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupplierGenerateResource\Pages;
use App\Models\Supplier;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\{TextInput, Toggle, Grid, Section, Textarea};
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Support\Facades\Cache;

class SupplierGenerateResource extends Resource
{
    protected static ?string $model = Supplier::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $modelLabel = 'الموردين';

    protected static ?string $pluralModelLabel = 'الموردين';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('بيانات المورد')
                    ->description('أدخل بيانات المورد الأساسية')
                    ->schema([
                        Grid::make(12)->schema([
                            TextInput::make('code')
                                ->label('كود المورد')
                                ->required()
                                ->unique(ignoreRecord: true)
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
                                ->unique(ignoreRecord: true)
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
                                ->unique(ignoreRecord: true)
                                ->nullable()
                                ->columnSpan(4),

                            TextInput::make('country')->label('الدولة')->columnSpan(4),
                            TextInput::make('city')->label('المدينة')->columnSpan(4),
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
                                ->dehydrated(false) // خليه يتحدث من المعاملات لاحقاً
                                ->disabled()
                                ->columnSpan(6)
                                ->helperText('يُحدَّث آليًا من المشتريات والمدفوعات'),
                        ]),

                        Textarea::make('notes')
                            ->label('ملاحظات')
                            ->rows(3),
                    ])->columns(12),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->label('الكود')->searchable()->sortable(),
                TextColumn::make('name')->label('الاسم')->searchable()->sortable(),
                TextColumn::make('company_name')->label('الشركة')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('phone')->label('الهاتف')->copyable(),
                TextColumn::make('email')->label('البريد')->icon('heroicon-m-envelope')->toggleable(),
                TextColumn::make('current_balance')
                    ->label('الرصيد')
                    ->money('EGP', true)
                    ->badge()
                    ->color(fn($state) => $state > 0 ? 'danger' : ($state < 0 ? 'success' : 'gray')) // عليك / لك
                    ->sortable(),
                IconColumn::make('is_active')->label('نشِط')->boolean(),
                TextColumn::make('created_at')->label('أُنشئ')->dateTime()->since(),
            ])
            ->filters([
                TernaryFilter::make('is_active')->label('نشِط'),
                Tables\Filters\SelectFilter::make('balance_state')
                    ->label('حالة الرصيد')
                    ->options([
                        'positive' => 'رصيد مستحق للمورد (عليك)',
                        'zero'     => 'رصيد صفر',
                        'negative' => 'رصيد لك عند المورد',
                    ])
                    ->query(function ($query, $data) {
                        return match ($data['value'] ?? null) {
                            'positive' => $query->where('current_balance', '>', 0),
                            'zero'     => $query->where('current_balance', '=', 0),
                            'negative' => $query->where('current_balance', '<', 0),
                            default    => $query,
                        };
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('name');
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
            'index' => Pages\ListSupplierGenerates::route('/'),
            'create' => Pages\CreateSupplierGenerate::route('/create'),
            'edit' => Pages\EditSupplierGenerate::route('/{record}/edit'),
        ];
    }
}