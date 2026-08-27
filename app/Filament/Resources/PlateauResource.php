<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\PlateauResource\RelationManagers\EquipmentsRelationManager;
use App\Filament\Resources\PlateauResource\Pages\ListPlateaux;
use App\Filament\Resources\PlateauResource\Pages\CreatePlateau;
use App\Filament\Resources\PlateauResource\Pages\ViewPlateau;
use App\Filament\Resources\PlateauResource\Pages\EditPlateau;
use App\Filament\Resources\PlateauResource\Pages\PlateauPlanning;
use App\Filament\Resources\PlateauResource\Pages;
use App\Filament\Resources\PlateauResource\RelationManagers;
use App\Models\Plateau;
use App\Models\User;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;

class PlateauResource extends Resource
{
    protected static ?string $model = Plateau::class;

    protected static string | \BackedEnum | null $navigationIcon   = 'fas-border-all';
    protected static ?string $navigationLabel  = 'Plateaux';
    protected static ?int $navigationSort      = 20;
    protected static string | \UnitEnum | null $navigationGroup  = 'Gestion';
    protected static ?string $modelLabel       = 'Plateau';
    protected static ?string $pluralModelLabel = 'Plateaux';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('manager_id')
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
                TextInput::make('name')
                    ->label(__('attributes.name'))
                    ->required()
                    ->maxLength(255),
                ColorPicker::make('color')
                    ->label(__('attributes.color'))
                    ->nullable(),
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
                TextColumn::make('manager_id')
                    ->label(__('attributes.manager'))
                    ->formatStateUsing(
                        fn (Plateau $record): string => $record->manager?->name ?? '?'
                    )
                    ->sortable(),
                ColorColumn::make('color')
                    ->label(__('attributes.color'))
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('attributes.name'))
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('attributes.created_at'))
                    ->sortable()
                    ->dateTime('d/m/Y H:i:s'),
                TextColumn::make('updated_at')
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
                layout: FiltersLayout::AboveContentCollapsible
            )
            ->recordActions([
                ViewAction::make(),
                Action::make('planning')
                    ->label(__('actions.planning'))
                    ->url(fn (Plateau $record) => route('filament.admin.resources.plateaux.planning', ['record' => $record]))
                    ->color(Color::Lime)
                    ->icon('fas-calendar'),
                EditAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            EquipmentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'    => ListPlateaux::route('/'),
            'create'   => CreatePlateau::route('/create'),
            'view'     => ViewPlateau::route('/{record}'),
            'edit'     => EditPlateau::route('/{record}/edit'),
            'planning' => PlateauPlanning::route('/{record}/planning'),
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
