<?php

namespace App\Filament\Resources\EquipmentResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PlateauxRelationManager extends RelationManager
{
    protected static string $relationship = 'plateaux';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('attributes.name'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->label(__('attributes.quantity'))
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            // ->headerActions([
            //     Tables\Actions\CreateAction::make(),
            //     Tables\Actions\AttachAction::make(),
            // ])
            // ->actions([
            //     Tables\Actions\EditAction::make(),
            //     Tables\Actions\DetachAction::make(),
            //     Tables\Actions\DeleteAction::make(),
            // ])
            // ->bulkActions([
            //     Tables\Actions\DetachBulkAction::make(),
            //     Tables\Actions\DeleteBulkAction::make(),
            // ])
        ;
    }

    protected function isTablePaginationEnabled(): bool
    {
        return false;
    }

    protected function getTableEmptyStateHeading(): ?string
    {
        return __('messages.no_plateaux_title');
    }
}
