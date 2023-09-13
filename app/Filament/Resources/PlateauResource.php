<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlateauResource\Pages;
use App\Filament\Resources\PlateauResource\RelationManagers;
use App\Models\Plateau;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;

class PlateauResource extends Resource
{
    protected static ?string $model = Plateau::class;

    protected static ?string $navigationIcon   = 'fas-border-all';
    protected static ?string $navigationLabel  = 'Plateaux';
    protected static ?int $navigationSort      = 20;
    protected static ?string $navigationGroup  = 'Gestion';
    protected static ?string $modelLabel       = 'Plateau';
    protected static ?string $pluralModelLabel = 'Plateaux';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('manager_id')
                    ->label(__('attributes.manager'))
                    ->options(
                        User::role('plateau_manager')
                            ->get()
                            ->pluck('name', 'id')
                            ->all()
                    )
                    ->preload()
                    ->searchable()
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->label(__('attributes.name'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\RichEditor::make('description')
                    ->label(__('attributes.description'))
                    ->required()
                    ->disableAllToolbarButtons()
                    ->enableToolbarButtons(['bold', 'italic', 'strike', 'link', 'bulletList', 'orderedList'])
                    ->columnSpan(2),
                Forms\Components\SpatieMediaLibraryFileUpload::make('photos')
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
                Tables\Columns\TextColumn::make('manager_id')
                    ->label(__('attributes.manager'))
                    ->formatStateUsing(
                        fn (Plateau $record): string => $record->manager->name
                    )
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('attributes.name'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('attributes.created_at'))
                    ->sortable()
                    ->dateTime('d/m/Y H:i:s'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('attributes.updated_at'))
                    ->sortable()
                    ->dateTime('d/m/Y H:i:s'),
            ])
            ->filters(
                [
                    SelectFilter::make('manager_id')
                        ->label(__('attributes.manager'))
                        ->options(
                            User::all()->pluck('name', 'id')->unique()->all()
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
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\EquipmentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPlateaux::route('/'),
            'create' => Pages\CreatePlateau::route('/create'),
            'view'   => Pages\ViewPlateau::route('/{record}'),
            'edit'   => Pages\EditPlateau::route('/{record}/edit'),
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
