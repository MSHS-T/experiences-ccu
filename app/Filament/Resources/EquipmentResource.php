<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\EquipmentResource\RelationManagers\PlateauxRelationManager;
use App\Filament\Resources\EquipmentResource\Pages\ListEquipment;
use App\Filament\Resources\EquipmentResource\Pages\CreateEquipment;
use App\Filament\Resources\EquipmentResource\Pages\ViewEquipment;
use App\Filament\Resources\EquipmentResource\Pages\EditEquipment;
use App\Filament\Resources\EquipmentResource\Pages;
use App\Filament\Resources\EquipmentResource\RelationManagers;
use App\Models\Equipment;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;

class EquipmentResource extends Resource
{
    protected static ?string $model = Equipment::class;

    protected static string | \BackedEnum | null $navigationIcon   = 'fas-screwdriver-wrench';
    protected static ?string $navigationLabel  = 'Équipements';
    protected static ?int $navigationSort      = 30;
    protected static string | \UnitEnum | null $navigationGroup  = 'Gestion';
    protected static ?string $modelLabel       = 'Équipement';
    protected static ?string $pluralModelLabel = 'Équipements';


    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->schema([
                TextInput::make('name')
                    ->label(__('attributes.name'))
                    ->required()
                    ->maxLength(255)
                    ->columnSpan(2),
                TextInput::make('type')
                    ->label(__('attributes.type'))
                    ->required()
                    ->maxLength(255),
                TextInput::make('quantity')
                    ->label(__('attributes.quantity'))
                    ->required()
                    ->integer()
                    ->minValue(0)
                    ->step(1),
                RichEditor::make('description')
                    ->label(__('attributes.description'))
                    ->required()
                    ->disableAllToolbarButtons()
                    ->enableToolbarButtons(['bold', 'italic', 'strike', 'link', 'bulletList', 'orderedList'])
                    ->columnSpan(2),
                SpatieMediaLibraryFileUpload::make('photos')
                    ->label(__('attributes.photos'))
                    ->multiple()
                    ->enableReordering()
                    ->image()
                    ->columnSpan(2),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('attributes.name'))
                    ->sortable(),
                TextColumn::make('type')
                    ->label(__('attributes.type'))
                    ->sortable(),
                TextColumn::make('quantity')
                    ->label(__('attributes.quantity'))
                    ->sortable(),
                // Tables\Columns\TextColumn::make('description'),
                TextColumn::make('created_at')
                    ->label(__('attributes.created_at'))
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label(__('attributes.updated_at'))
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
            ])
            ->filters(
                [
                    SelectFilter::make('type')
                        ->options(
                            Equipment::all()->pluck('type', 'type')->unique()->all()
                        )
                ],
                layout: FiltersLayout::AboveContentCollapsible
            )
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ])
            ->defaultSort('name');
    }

    public static function getRelations(): array
    {
        return [
            PlateauxRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListEquipment::route('/'),
            'create' => CreateEquipment::route('/create'),
            'view'   => ViewEquipment::route('/{record}'),
            'edit'   => EditEquipment::route('/{record}/edit'),
        ];
    }

    protected function getDefaultTableSortColumn(): ?string
    {
        return 'name';
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return 'asc';
    }

    protected function shouldPersistTableSortInSession(): bool
    {
        return true;
    }
}
