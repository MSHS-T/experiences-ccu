<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ManipulationResource\Pages;
use App\Models\Manipulation;
use App\Models\Plateau;
use App\Models\User;
use App\Utils\SlotGenerator;
use Awcodes\FilamentTableRepeater\Components\TableRepeater;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Support\Enums\ActionSize;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ManipulationResource extends Resource
{
    protected static ?string $model = Manipulation::class;

    protected static ?string $navigationIcon   = 'fas-flask-vial';
    protected static ?string $navigationLabel  = 'Manipulations';
    protected static ?int $navigationSort      = 10;
    protected static ?string $navigationGroup  = 'Plateforme';
    protected static ?string $modelLabel       = 'Manipulation';
    protected static ?string $pluralModelLabel = 'Manipulations';

    public static function form(Form $form): Form
    {
        $computeSlotCount = fn (callable $set, callable $get) => $set(
            'slot_count',
            SlotGenerator::estimateCount(
                Plateau::find(intval($get('plateau_id'))),
                $get('start_date'),
                $get('end_date'),
                $get('available_hours'),
                $get('duration'),
            )
        );
        $clearHalfDayAction = fn (string $fieldName) => Action::make('emptyHalfDay')
            ->icon('fas-eraser')
            ->label(__('actions.clear'))
            ->action(fn (Set $set) => $set($fieldName, null));

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
                        'md'      => 2
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
                Forms\Components\Select::make('users')
                    ->label(__('attributes.manipulation_managers'))
                    ->columnSpan([
                        'default' => 1,
                        'md'      => 2
                    ])
                    ->relationship('users', 'id')
                    // ->formatStateUsing()
                    ->multiple()
                    ->options(User::role('manipulation_manager')->get()->pluck('name', 'id')->unique()->all())
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
                    // ->required()
                    ->minValue(1)
                    ->suffixAction(
                        Action::make('refreshSlotCount')
                            ->icon('fas-arrows-rotate')
                            ->action($computeSlotCount)
                    ),
                TableRepeater::make('requirements')
                    ->label(__('attributes.requirements'))
                    ->emptyLabel(__('messages.no_requirement'))
                    ->addActionLabel(__('messages.add_requirement'))
                    ->columnSpan('full')
                    ->hideLabels()
                    ->defaultItems(1)
                    ->withoutHeader()
                    ->formatStateUsing(
                        fn (?Manipulation $record) => collect($record?->requirements ?? [])->map(fn ($r) => ['text' => $r])->all()
                    )
                    ->reorderable(false)
                    ->schema([
                        Forms\Components\TextInput::make('text')
                            ->required()
                            ->columnSpan('full')
                            ->hiddenLabel()
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
                                    ->columns(1)
                                    ->visible()
                                    ->schema([
                                        Forms\Components\TimePicker::make("available_hours.$day.start_am")
                                            ->label(__('attributes.start_am'))
                                            ->default(config('collabccu.default_hours.start_am'))
                                            ->seconds(false)
                                            ->format('H:i')
                                            ->displayFormat('H:i')
                                            ->hintAction($clearHalfDayAction("available_hours.$day.start_am")),
                                        Forms\Components\TimePicker::make("available_hours.$day.end_am")
                                            ->label(__('attributes.end_am'))
                                            ->default(config('collabccu.default_hours.end_am'))
                                            ->seconds(false)
                                            ->format('H:i')
                                            ->displayFormat('H:i')
                                            ->hintAction($clearHalfDayAction("available_hours.$day.end_am"))
                                            ->requiredWith("available_hours.$day.start_am"),
                                        Forms\Components\TimePicker::make("available_hours.$day.start_pm")
                                            ->label(__('attributes.start_pm'))
                                            ->default(config('collabccu.default_hours.start_pm'))
                                            ->seconds(false)
                                            ->format('H:i')
                                            ->displayFormat('H:i')
                                            ->hintAction($clearHalfDayAction("available_hours.$day.start_pm"))
                                            ->nullable()
                                            ->after("available_hours.$day.end_am"),
                                        Forms\Components\TimePicker::make("available_hours.$day.end_pm")
                                            ->label(__('attributes.end_pm'))
                                            ->default(config('collabccu.default_hours.end_pm'))
                                            ->seconds(false)
                                            ->format('H:i')
                                            ->displayFormat('H:i')
                                            ->hintAction($clearHalfDayAction("available_hours.$day.end_pm"))
                                            ->requiredWith("available_hours.$day.start_pm"),
                                        Forms\Components\Actions::make([
                                            Action::make('clearDay' . $day)
                                                ->label(__('actions.clear_day'))
                                                ->icon('fas-eraser')
                                                ->color('warning')
                                                ->size(ActionSize::ExtraSmall)
                                                ->action(function (Set $set) use ($day) {
                                                    $set("available_hours.$day.start_am", null);
                                                    $set("available_hours.$day.end_am", null);
                                                    $set("available_hours.$day.start_pm", null);
                                                    $set("available_hours.$day.end_pm", null);
                                                })
                                        ])->alignCenter()
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
                Tables\Columns\TextColumn::make('plateau.name')
                    ->label(__('attributes.plateau'))
                    // ->formatStateUsing(
                    //     fn (Manipulation $record): string => $record->plateau->name
                    // )
                    ->sortable(),
                Tables\Columns\TextColumn::make('users.id')
                    ->label(__('attributes.manipulation_managers'))
                    ->formatStateUsing(
                        fn (Manipulation $record) => Str::of(
                            sprintf(
                                '<ul class="list-disc">%s</ul>',
                                $record->users->map(fn (User $u) => $u->name)
                                    ->map(fn ($d) => '<li>' . $d . '</li>')
                                    ->join('')
                            )
                        )->sanitizeHtml()->toHtmlString()
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
                        ),
                    Filter::make('users')
                        ->form([
                            Forms\Components\Select::make('user_id')
                                ->label(__('attributes.manipulation_managers'))
                                ->options(
                                    User::role('manipulation_manager')->get()->pluck('name', 'id')->unique()->all()
                                ),
                        ])
                        ->query(function (Builder $query, array $data): Builder {
                            return $query
                                ->when(
                                    $data['user_id'],
                                    fn (Builder $query): Builder => $query->whereHas(
                                        'users',
                                        fn (Builder $query) => $query->where('id', $data['user_id'])
                                    ),
                                );
                        })
                ],
                layout: \Filament\Tables\Enums\FiltersLayout::AboveContent
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
                    ->hidden(fn (Manipulation $record) => !Auth::user()->can('publish', $record))
                    ->disabled(fn (Manipulation $record) => !Auth::user()->can('publish', $record)),
                Tables\Actions\Action::make('archive')
                    ->label(__('actions.archive'))
                    ->icon('fas-calendar-check')
                    ->action(fn (Manipulation $record) => $record->archive())
                    ->requiresConfirmation()
                    ->color('danger')
                    ->hidden(fn (Manipulation $record) => !Auth::user()->can('archive', $record))
                    ->disabled(fn (Manipulation $record) => !Auth::user()->can('archive', $record)),
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

    public static function getWidgets(): array
    {
        return [
            ManipulationResource\Widgets\AttributionOverview::class,
        ];
    }

    public static function getNavigationBadge(): ?string
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
