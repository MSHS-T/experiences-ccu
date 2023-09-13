<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EquipmentResource\Pages;
use App\Filament\Resources\EquipmentResource\RelationManagers;
use App\Models\Equipment;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;

class EquipmentResource extends Resource
{
    protected static ?string $model = Equipment::class;

    protected static ?string $navigationIcon   = 'fas-screwdriver-wrench';
    protected static ?string $navigationLabel  = 'Équipements';
    protected static ?int $navigationSort      = 30;
    protected static ?string $navigationGroup  = 'Gestion';
    protected static ?string $modelLabel       = 'Équipement';
    protected static ?string $pluralModelLabel = 'Équipements';


    public static function form(Form $form): Form
    {
        return $form
            ->columns(2)
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('attributes.name'))
                    ->required()
                    ->maxLength(255)
                    ->columnSpan(2),
                Forms\Components\TextInput::make('type')
                    ->label(__('attributes.type'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('quantity')
                    ->label(__('attributes.quantity'))
                    ->required()
                    ->integer()
                    ->minValue(0)
                    ->step(1),
                Forms\Components\RichEditor::make('description')
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
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('attributes.name'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label(__('attributes.type'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->label(__('attributes.quantity'))
                    ->sortable(),
                // Tables\Columns\TextColumn::make('description'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('attributes.created_at'))
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
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
                layout: \Filament\Tables\Enums\FiltersLayout::AboveContentCollapsible
            )
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('name');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PlateauxRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListEquipment::route('/'),
            'create' => Pages\CreateEquipment::route('/create'),
            'view'   => Pages\ViewEquipment::route('/{record}'),
            'edit'   => Pages\EditEquipment::route('/{record}/edit'),
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
