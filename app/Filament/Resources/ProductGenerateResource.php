<?php

// namespace App\Filament\Resources;

// use App\Filament\Resources\ProductGenerateResource\Pages;
// use App\Models\Product;
// use Filament\Forms;
// use Filament\Forms\Form;
// use Filament\Resources\Resource;
// use Filament\Tables;
// use Filament\Tables\Table;

// class ProductGenerateResource extends Resource
// {
//     protected static ?string $model = Product::class;

//     protected static ?string $navigationGroup = 'إدارة المخزون';
//     protected static ?string $navigationIcon = 'heroicon-o-rectangle-group';
//     protected static ?string $modelLabel = 'فئه';
//     protected static ?string $pluralModelLabel = 'الفئات';

//     public static function form(Form $form): Form
//     {
//         return $form->schema([
//             Forms\Components\Section::make('بيانات المنتج')
//                 ->description('أدخل التفاصيل الأساسية للمنتج')
//                 ->schema([
//                     Forms\Components\TextInput::make('name')
//                         ->label('اسم المنتج')
//                         ->required()
//                         ->unique(ignoreRecord: true)
//                         ->prefixIcon('heroicon-o-tag')
//                         ->placeholder('مثال: تيشيرت قطن'),

//                     Forms\Components\TextInput::make('brand')
//                         ->label('الماركة / البراند')
//                         ->prefixIcon('heroicon-o-building-storefront')
//                         ->placeholder('مثال: LocalBrand'),

//                     // Forms\Components\Select::make('category_id')
//                     //     ->label('التصنيف')
//                     //     ->relationship('category', 'name')
//                     //     ->searchable()
//                     //     ->preload()
//                     //     ->placeholder('اختر التصنيف (اختياري)'),

//                     Forms\Components\TextInput::make('tax_rate')
//                         ->label('نسبة الضريبة %')
//                         ->numeric()
//                         ->default(0)
//                         ->hint('مثال: 14'),

//                     Forms\Components\Toggle::make('is_active')
//                         ->label('مُفعل؟')
//                         ->default(true),
//                 ])
//                 ->columns(2)
//                 ->collapsible(),
//         ]);
//     }

//     public static function table(Table $table): Table
//     {
//         return $table
//             ->columns([
//                 Tables\Columns\TextColumn::make('id')
//                     ->label('#')
//                     ->sortable()
//                     ->toggleable(),

//                 Tables\Columns\TextColumn::make('name')
//                     ->label('الاسم')
//                     ->searchable()
//                     ->sortable(),

//                 Tables\Columns\TextColumn::make('brand')
//                     ->label('البراند')
//                     ->searchable()
//                     ->toggleable(),

//                 // Tables\Columns\TextColumn::make('category.name')
//                 //     ->label('التصنيف')
//                 //     ->badge()
//                 //     ->toggleable(),

//                 Tables\Columns\TextColumn::make('tax_rate')
//                     ->label('ضريبة %')
//                     ->sortable()
//                     ->toggleable(),

//                 Tables\Columns\IconColumn::make('is_active')
//                     ->label('مفعل')
//                     ->boolean(),

//                 Tables\Columns\TextColumn::make('variants_count')
//                     ->label('عدد المنتجات')
//                     ->counts('variants')
//                     ->badge(),
//             ])
//             ->filters([
//                 Tables\Filters\TernaryFilter::make('is_active')->label('نشط'),
//             ])
//             ->actions([
//                 Tables\Actions\ViewAction::make(),
//                 Tables\Actions\EditAction::make(),
//                 Tables\Actions\DeleteAction::make(),
//             ])
//             ->bulkActions([
//                 Tables\Actions\BulkActionGroup::make([
//                     Tables\Actions\DeleteBulkAction::make(),
//                 ]),
//             ]);
//     }

//     public static function getRelations(): array
//     {
//         // لو عايز تدير الـVariants من داخل المنتج مباشرة:
//         // اعمل Relation Manager للـvariants وأضفه هنا
//         return [
//             // RelationManagers\VariantsRelationManager::class,
//         ];
//     }

//     public static function getPages(): array
//     {
//         return [
//             'index'  => Pages\ListProductGenerates::route('/'),
//             'create' => Pages\CreateProductGenerate::route('/create'),
//             'edit'   => Pages\EditProductGenerate::route('/{record}/edit'),
//         ];
//     }

//     // Search عالمي من شريط البحث فوق
//     public static function getGloballySearchableAttributes(): array
//     {
//         return ['name', 'brand', 'category.name'];
//     }

//     public static function getGlobalSearchResultDetails(\Illuminate\Database\Eloquent\Model $record): array
//     {
//         /** @var \App\Models\Product $record */
//         return array_filter([
//             'براند'   => $record->brand,
//             'تصنيف'   => optional($record->category)->name,
//             'ضريبة %' => $record->tax_rate,
//         ]);
//     }
// }