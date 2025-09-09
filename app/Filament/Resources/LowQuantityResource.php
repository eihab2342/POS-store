<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LowQuantityResource\Pages;
use App\Models\ProductVariant;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LowQuantityResource extends Resource
{
    protected static ?string $model = ProductVariant::class;

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';
    protected static ?string $navigationGroup = 'إدارة المخزون';
    protected static ?string $pluralModelLabel = 'مخزون قليل/انتهى';
    protected static ?string $navigationLabel = 'المخزون القليل';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // مش محتاج فورم هنا بما إننا هنعملها للعرض فقط
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('اسم المنتج')
                    ->searchable(),

                Tables\Columns\TextColumn::make('stock_qty')
                    ->label('الكمية')
                    ->sortable()
                    ->color(
                        fn($record) =>
                        $record->stock_qty <= 0
                            ? 'danger'
                            : ($record->stock_qty <= $record->reorder_level ? 'warning' : 'success')
                    )
                    ->formatStateUsing(
                        fn($record) =>
                        $record->stock_qty <= 0
                            ? 'انتهى'
                            : $record->stock_qty
                    ),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('آخر تحديث')
                    ->dateTime('d/m/Y H:i'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        'out' => 'انتهى',
                        'low' => 'قليل',
                    ])
                    ->query(function (Builder $query, $state) {
                        if ($state === 'out') {
                            $query->where('stock_qty', '<=', 0);
                        } elseif ($state === 'low') {
                            $query->whereColumn('stock_qty', '<=', 'reorder_level')
                                ->where('stock_qty', '>', 0);
                        }
                    }),
            ])
            ->actions([
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
            ->where('stock_qty', '<=', 'reorder_level'); 
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLowQuantities::route('/'),
        ];
    }
}