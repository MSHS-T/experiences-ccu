<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ManipulationResource\Pages;
use App\Filament\Resources\ManipulationResource\RelationManagers;
use App\Forms\Components\SimpleRepeater;
use App\Models\Manipulation;
use App\Models\Plateau;
use App\Utils\SlotGenerator;
use Awcodes\FilamentTableRepeater\Components\TableRepeater;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Filters\Layout;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ManipulationResource extends Resource
{
    const DEFAULT_HOURS = [
        'start_am' => '09:00',
        'end_am'   => '12:00',
        'start_pm' => '14:00',
        'end_pm'   => '17:00',
    ];

    protected static ?string $model = Manipulation::class;

    protected static ?string $navigationIcon   = 'fas-flask-vial';
    protected static ?string $navigationLabel  = 'Manipulations';
    protected static ?int $navigationSort      = 10;
    protected static ?string $modelLabel       = 'Manipulation';
    protected static ?string $pluralModelLabel = 'Manipulations';

    public static function form(Form $form): Form
    {
        $computeSlotCount = fn (callable $set, callable $get) => $set(
            'slot_count',
            SlotGenerator::estimateCount(
                $get('start_date'),
                $get('end_date'),
                $get('available_hours'),
                $get('duration'),
            )
        );
        return $form
            ->columns([
                'default' => 1,
                'md'      => 2,
                'lg'      => 4
            ])
            ->schema([
                Forms\Components\Select::make('plateau_id')
                    ->label(__('attributes.plateau'))
                    ->columnSpan([
                        'default' => 1,
                        'md'      => 1
                    ])
                    ->relationship('plateau', 'name')
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->label(__('attributes.name'))
                    ->columnSpan([
                        'default' => 1,
                        'md'      => 2
                    ])
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('location')
                    ->label(__('attributes.location'))
                    ->maxLength(255)
                    ->required(),
                Forms\Components\RichEditor::make('description')
                    ->label(__('attributes.description'))
                    ->required()
                    ->disableAllToolbarButtons()
                    ->enableToolbarButtons(['bold', 'italic', 'strike', 'link', 'bulletList', 'orderedList'])
                    ->columnSpan('full'),
                Forms\Components\DatePicker::make('start_date')
                    ->label(__('attributes.start_date'))
                    ->displayFormat('d/m/Y')
                    ->reactive()
                    ->afterStateUpdated($computeSlotCount)
                    ->required(),
                Forms\Components\DatePicker::make('end_date')
                    ->label(__('attributes.end_date'))
                    ->displayFormat('d/m/Y')
                    ->after('start_date')
                    ->reactive()
                    ->afterStateUpdated($computeSlotCount)
                    ->required(),
                Forms\Components\TextInput::make('duration')
                    ->label(__('attributes.duration'))
                    ->suffix('minutes')
                    ->integer()
                    ->minValue(1)
                    ->reactive()
                    ->afterStateUpdated($computeSlotCount)
                    ->required(),
                Forms\Components\TextInput::make('slot_count')
                    ->label(__('attributes.slot_count'))
                    ->disabled()
                    ->required()
                    ->minValue(1),
                TableRepeater::make('requirements')
                    ->label(__('attributes.requirements'))
                    ->emptyLabel(__('messages.no_requirement'))
                    ->createItemButtonLabel(__('messages.add_requirement'))
                    ->columnSpan('full')
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
                            ->columnSpan('full')
                            ->disableLabel()
                            ->maxLength(255)
                    ]),
                Forms\Components\Section::make('available_hours')
                    ->heading(__('attributes.available_hours'))
                    ->description(Str::of(__('messages.available_hours_description'))->sanitizeHtml()->toHtmlString())
                    ->extraAttributes(['class' => '!bg-gray-100 dark:!bg-gray-800'])
                    ->compact()
                    ->columnSpan([
                        'default' => 1,
                        'md'      => 'full'
                    ])
                    ->columns([
                        'default' => 1,
                        'md'      => 3,
                        'xl'      => 5
                    ])
                    ->schema(
                        collect(['monday', 'tuesday', 'wednesday', 'thursday', 'friday'])
                            ->map(
                                fn ($day) => Forms\Components\Fieldset::make('available_hours.monday')
                                    ->label(__('attributes.' . $day))
                                    ->columnSpan(1)
                                    ->columns(2)
                                    ->schema([
                                        Forms\Components\TimePicker::make("available_hours.$day.start_am")
                                            ->label(__('attributes.start_am'))
                                            ->default(self::DEFAULT_HOURS['start_am'])
                                            ->withoutSeconds()
                                            ->format('H:i')
                                            ->displayFormat('H:i'),
                                        Forms\Components\TimePicker::make("available_hours.$day.end_am")
                                            ->label(__('attributes.end_am'))
                                            ->default(self::DEFAULT_HOURS['end_am'])
                                            ->withoutSeconds()
                                            ->format('H:i')
                                            ->displayFormat('H:i')
                                            ->requiredWith("available_hours.$day.start_am"),
                                        Forms\Components\TimePicker::make("available_hours.$day.start_pm")
                                            ->label(__('attributes.start_pm'))
                                            ->default(self::DEFAULT_HOURS['start_pm'])
                                            ->withoutSeconds()
                                            ->format('H:i')
                                            ->displayFormat('H:i')
                                            ->nullable()
                                            ->after("available_hours.$day.end_am"),
                                        Forms\Components\TimePicker::make("available_hours.$day.end_pm")
                                            ->label(__('attributes.end_pm'))
                                            ->default(self::DEFAULT_HOURS['end_pm'])
                                            ->withoutSeconds()
                                            ->format('H:i')
                                            ->displayFormat('H:i')
                                            ->requiredWith("available_hours.$day.start_pm"),
                                    ])
                            )
                            ->all()
                    ),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable(),
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
                Tables\Columns\TextColumn::make('slots_count')
                    ->counts('slots')
                    ->label(__('attributes.slot_count'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->label(__('attributes.start_date'))
                    ->sortable()
                    ->date('d/m/Y'),
                Tables\Columns\TextColumn::make('end_date')
                    ->label(__('attributes.end_date'))
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
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(
                        fn (Manipulation $record) => Str::of(
                            sprintf(
                                '<ul class="list-disc">%s</ul>',
                                collect($record->getAvailableHoursDisplay())
                                    ->map(fn ($d) => '<li>' . $d . '</li>')
                                    ->join('')
                            )
                        )->sanitizeHtml()->toHtmlString()
                    ),
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
                Tables\Actions\Action::make('toggle_published')
                    ->label(fn (Manipulation $record) => $record->published ? __('actions.unpublish') : __('actions.publish'))
                    ->icon(fn (Manipulation $record) => $record->published ? 'fas-calendar-xmark' : 'fas-calendar-check')
                    ->action(fn (Manipulation $record) => $record->togglePublished())
                    ->requiresConfirmation()
                    ->color(fn (Manipulation $record) => $record->published ? 'warning' : 'success')
                    ->hidden(
                        fn (Manipulation $record) => !(Auth::user()->hasRole('administrator')
                            || (Auth::user()->can('manipulation.publish') && $record->plateau->manager_id !== Auth::id())
                        )
                    )
                    ->disabled(
                        fn (Manipulation $record) => !(Auth::user()->hasRole('administrator')
                            || (Auth::user()->can('manipulation.publish') && $record->plateau->manager_id !== Auth::id())
                        )
                    ),
                Tables\Actions\Action::make('archive')
                    ->label(__('actions.archive'))
                    ->icon('fas-calendar-check')
                    ->action(fn (Manipulation $record) => $record->archive())
                    ->requiresConfirmation()
                    ->color('danger')
                    ->hidden(fn (Manipulation $record) => !Auth::user()->can('manipulation.archive') || $record->end_date->isAfter(Carbon::now()))
                    ->disabled(fn (Manipulation $record) => !Auth::user()->can('manipulation.archive') || $record->end_date->isAfter(Carbon::now())),
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
        return static::getModel()::wherePublished(true)->whereArchived(false)->count();
    }

    protected function getDefaultTableSortColumn(): ?string
    {
        return 'id';
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return 'desc';
    }

    protected function shouldPersistTableSortInSession(): bool
    {
        return true;
    }
}
