<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerGenerateResource\Pages;
use App\Filament\Resources\CustomerGenerateResource\RelationManagers;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CustomerGenerateResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $pluralModelLabel = 'العملاء';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('phone')
                    ->label('Phone')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                BadgeColumn::make('points')
                    ->sortable()
                    ->formatStateUsing(fn($state) => (int) $state)
                    ->extraAttributes(['class' => 'font-semibold']),

                TextColumn::make('sales_count')
                    ->label('Sales')
                    ->counts('sale') 
                    ->sortable(),
            ])
            ->filters([
                Filter::make('has_sales')
                    ->label('Has Sales')
                    ->query(fn(Builder $q) => $q->has('sales')),

                Filter::make('vip')
                    ->label('Points ≥ 1000')
                    ->query(fn(Builder $q) => $q->where('points', '>=', 1000)),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('addPoints')
                        ->label('Add Points')
                        ->form([
                            \Filament\Forms\Components\TextInput::make('points')
                                ->numeric()
                                ->required()
                                ->minValue(1),
                        ])
                        ->action(function (array $data, $records) {
                            foreach ($records as $customer) {
                                $customer->increment('points', (int) $data['points']);
                            }
                        }),
                ]),
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
            'index' => Pages\ListCustomerGenerates::route('/'),
            'create' => Pages\CreateCustomerGenerate::route('/create'),
            'edit' => Pages\EditCustomerGenerate::route('/{record}/edit'),
        ];
    }
}