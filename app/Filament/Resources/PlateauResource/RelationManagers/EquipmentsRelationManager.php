<?php

namespace App\Filament\Resources\PlateauResource\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables;

class EquipmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'equipments';

    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $modelLabel       = 'Équipement';
    protected static ?string $pluralModelLabel = 'Équipements';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('attributes.name'))
                    ->sortable(),
                TextColumn::make('pivot.quantity')
                    ->label(__('attributes.quantity'))
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                AttachAction::make()
                    ->preloadRecordSelect()
                    ->form(fn (AttachAction $action): array => [
                        $action->getRecordSelect(),
                        TextInput::make('quantity')
                            ->label(__('attributes.quantity'))
                            ->required()
                            ->integer()
                            ->minValue(0)
                            ->step(1),
                    ]),
            ])
            ->recordActions([
                DetachAction::make(),
            ])
            ->toolbarActions([
                DetachBulkAction::make(),
                DeleteBulkAction::make(),
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
