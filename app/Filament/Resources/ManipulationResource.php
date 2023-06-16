<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ManipulationResource\Pages;
use App\Filament\Resources\ManipulationResource\RelationManagers;
use App\Forms\Components\SimpleRepeater;
use App\Models\Manipulation;
use App\Models\Plateau;
use Awcodes\FilamentTableRepeater\Components\TableRepeater;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Filters\Layout;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class ManipulationResource extends Resource
{
    protected static ?string $model = Manipulation::class;

    protected static ?string $navigationIcon   = 'fas-flask-vial';
    protected static ?string $navigationLabel  = 'Manipulations';
    protected static ?int $navigationSort      = 10;
    protected static ?string $modelLabel       = 'Manipulation';
    protected static ?string $pluralModelLabel = 'Manipulations';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(4)
            ->schema([
                Forms\Components\Select::make('plateau_id')
                    ->label(__('attributes.plateau'))
                    ->columnSpan(2)
                    ->relationship('plateau', 'name')
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->label(__('attributes.name'))
                    ->columnSpan(2)
                    ->required()
                    ->maxLength(255),
                Forms\Components\RichEditor::make('description')
                    ->label(__('attributes.description'))
                    ->required()
                    ->disableAllToolbarButtons()
                    ->enableToolbarButtons(['bold', 'italic', 'strike', 'link', 'bulletList', 'orderedList'])
                    ->columnSpan(4),
                Forms\Components\DatePicker::make('start_date')
                    ->label(__('attributes.start_date'))
                    ->displayFormat('d/m/Y')
                    ->required(),
                Forms\Components\TextInput::make('location')
                    ->label(__('attributes.location'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('duration')
                    ->label(__('attributes.duration'))
                    ->suffix('minutes')
                    ->required(),
                Forms\Components\TextInput::make('target_slots')
                    ->label(__('attributes.target_slots'))
                    ->required(),
                TableRepeater::make('requirements')
                    ->label(__('attributes.requirements'))
                    ->emptyLabel(__('messages.no_requirement'))
                    ->createItemButtonLabel(__('messages.add_requirement'))
                    ->columnSpan(4)
                    ->hideLabels()
                    ->defaultItems(1)
                    ->withoutHeader()
                    ->formatStateUsing(
                        fn (?Manipulation $record) => collect($record?->requirements ?? [])->map(fn ($r) => ['text' => $r])->all()
                    )
                    ->disableItemMovement()
                    ->schema([
                        Forms\Components\TextInput::make('text')
                            ->required()
                            ->disableLabel()
                            ->maxLength(255)
                    ]),
                Forms\Components\TextInput::make('available_hours')
                    ->label(__('attributes.available_hours'))
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('plateau_id')
                    ->label(__('attributes.plateau'))
                    ->formatStateUsing(
                        fn (Manipulation $record): string => $record->plateau->name
                    )
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('attributes.name'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('duration')
                    ->label(__('attributes.duration'))
                    ->formatStateUsing(
                        fn (Manipulation $record): string => $record->duration . ' min'
                    )
                    ->sortable(),
                Tables\Columns\TextColumn::make('target_slots')
                    ->label(__('attributes.target_slots'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->label(__('attributes.start_date'))
                    ->sortable()
                    ->date('d/m/Y'),
                Tables\Columns\TextColumn::make('location')
                    ->label(__('attributes.location'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\IconColumn::make('published')
                    ->label(__('attributes.published'))
                    ->boolean(),
                Tables\Columns\TextColumn::make('available_hours_str')
                    ->label(__('attributes.available_hours'))
                    ->formatStateUsing(
                        fn (Manipulation $record): string => $record->getOpeningHoursDisplay()
                    )
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('requirements_str')
                    ->label(__('attributes.requirements'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(
                        fn (Manipulation $record) => Str::of(
                            sprintf(
                                '<ul class="list-disc">%s</ul>',
                                collect($record->requirements)
                                    ->map(fn ($r) => '<li>' . $r . '</li>')
                                    ->join('')
                            )
                        )->sanitizeHtml()->toHtmlString()
                    ),
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
                    SelectFilter::make('plateau_id')
                        ->label(__('attributes.plateau'))
                        ->options(
                            Plateau::all()->pluck('name', 'id')->unique()->all()
                        )
                ],
                layout: Layout::AboveContentCollapsible
            )
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('toggle_enabled')
                    ->label(fn (Manipulation $record) => $record->published ? __('actions.unpublish') : __('actions.publish'))
                    ->icon(fn (Manipulation $record) => $record->published ? 'fas-calendar-xmark' : 'fas-calendar-check')
                    ->action(fn (Manipulation $record) => $record->togglePublished())
                    ->requiresConfirmation()
                    ->color(fn (Manipulation $record) => $record->published ? 'warning' : 'success'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index'  => Pages\ListManipulations::route('/'),
            'create' => Pages\CreateManipulation::route('/create'),
            'view'   => Pages\ViewManipulation::route('/{record}'),
            'edit'   => Pages\EditManipulation::route('/{record}/edit'),
        ];
    }

    protected static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
