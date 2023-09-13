<?php

namespace App\Filament\Resources\PlateauResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables;

class EquipmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'equipments';

    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $modelLabel       = 'Équipement';
    protected static ?string $pluralModelLabel = 'Équipements';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('attributes.name'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('pivot.quantity')
                    ->label(__('attributes.quantity'))
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->preloadRecordSelect()
                    ->form(fn (Tables\Actions\AttachAction $action): array => [
                        $action->getRecordSelect(),
                        Forms\Components\TextInput::make('quantity')
                            ->label(__('attributes.quantity'))
                            ->required()
                            ->integer()
                            ->minValue(0)
                            ->step(1),
                    ]),
            ])
            ->actions([
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DetachBulkAction::make(),
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    protected function isTablePaginationEnabled(): bool
    {
        return false;
    }

    protected function getTableEmptyStateHeading(): ?string
    {
        return __('messages.no_equipments_title');
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        return __('messages.no_equipments_description');
    }
}
