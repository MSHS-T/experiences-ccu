<?php

namespace App\Filament\Resources;

use App\Filament\Pages\Attendance;
use App\Filament\Resources\ManipulationResource\Pages;
use App\Models\Attribution;
use App\Models\Booking;
use App\Models\Manipulation;
use App\Models\ManipulationStatistics;
use App\Models\Plateau;
use App\Models\Slot;
use App\Models\User;
use App\Utils\SlotGenerator;
use Awcodes\FilamentTableRepeater\Components\TableRepeater;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\ActionSize;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ManipulationResource extends Resource
{
    protected static ?string $model = Manipulation::class;

    protected static ?string $navigationIcon   = 'fas-flask-vial';
    protected static ?string $navigationLabel  = 'Expériences';
    protected static ?int $navigationSort      = 10;
    protected static ?string $navigationGroup  = 'Plateforme';
    protected static ?string $modelLabel       = 'Expérience';
    protected static ?string $pluralModelLabel = 'Expériences';

    public static function form(Form $form): Form
    {
        $computeSlotCount = fn(callable $set, callable $get) => $set(
            'slot_count',
            SlotGenerator::estimateCount(
                $get('users') ?? [],
                Plateau::find(intval($get('plateau_id'))),
                $get('start_date'),
                $get('end_date'),
                $get('duration'),
                $form->getRecord()?->id
            )
        );

        return $form
            ->columns([
                'default' => 1,
                'md'      => 2,
                'lg'      => 3
            ])
            ->schema([
                Forms\Components\Select::make('plateau_id')
                    ->label(__('attributes.plateau'))
                    ->columnSpan([
                        'default' => 1,
                        'md'      => 2,
                        'lg'      => 1,
                    ])
                    ->relationship(
                        name: 'plateau',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn(Builder $query) => $query
                            ->when(
                                Auth::user()->hasRole('manipulation_manager'),
                                fn(Builder $query) => $query->whereIn(
                                    'id',
                                    Attribution::where('manipulation_manager_id', Auth::id())
                                        ->where('end_date', '>=', today())
                                        ->pluck('plateau_id')
                                )
                            ),
                    )
                    ->required(),
                Forms\Components\Select::make('users')
                    ->label(__('attributes.manipulation_managers'))
                    ->columnSpan([
                        'default' => 1,
                        'md'      => 2,
                        'lg'      => 1,
                    ])
                    ->relationship('users', 'id')
                    ->reactive()
                    ->afterStateUpdated($computeSlotCount)
                    ->multiple()
                    ->options(User::role('manipulation_manager')->get()->pluck('name', 'id')->unique()->all())
                    ->default(Auth::user()->hasRole('manipulation_manager') ? [Auth::id()] : [])
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->label(__('attributes.name'))
                    ->columnSpan([
                        'default' => 1,
                        'md'      => 2,
                        'lg'      => 1,
                    ])
                    ->required()
                    ->maxLength(255),
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
                Forms\Components\TextInput::make('max_booking_per_slot')
                    ->label(__('attributes.max_booking_per_slot'))
                    ->suffix('personnes')
                    ->integer()
                    ->minValue(1)
                    ->required(),
                TableRepeater::make('requirements')
                    ->label(__('attributes.requirements'))
                    ->emptyLabel(__('messages.no_requirement'))
                    ->addActionLabel(__('messages.add_requirement'))
                    ->columnSpan('full')
                    ->hideLabels()
                    ->defaultItems(1)
                    ->withoutHeader()
                    ->formatStateUsing(
                        fn(?Manipulation $record) => collect($record?->requirements ?? [])->map(fn($r) => ['text' => $r])->all()
                    )
                    ->reorderable(false)
                    ->schema([
                        Forms\Components\TextInput::make('text')
                            ->required()
                            ->columnSpan('full')
                            ->hiddenLabel()
                            ->maxLength(255)
                    ]),
                Forms\Components\TextInput::make('slot_count')
                    ->label(__('attributes.generated_slot_count'))
                    ->disabled()
                    // ->required()
                    ->minValue(1)
                    ->suffixAction(
                        Action::make('refreshSlotCount')
                            ->icon('fas-arrows-rotate')
                            ->action($computeSlotCount)
                    ),
            ]);
    }

    public static function table(Table $table): Table
    {
        $plateaux = Plateau::all()
            ->filter(function (Plateau $plateau) {
                $user = Auth::user();
                /** @var User $user */
                if ($user->hasRole('administrator')) {
                    return true;
                }
                if ($user->hasRole('plateau_manager')) {
                    return $plateau->manager?->id === $user->id;
                }
                if ($user->hasRole('manipulation_manager')) {
                    return $user->attributions->some(fn(Attribution $attribution) => $attribution->plateau->id === $plateau->id);
                }
            });
        return $table
            ->modifyQueryUsing(
                fn(Builder $query) =>
                $query->with(['statistics'])
                    ->withCount(['slots'])
                    ->whereIn('plateau_id', $plateaux->pluck('id'))
                    ->when(Auth::user()->hasRole('manipulation_manager'), fn(Builder $query) => $query->whereHas('users', fn(Builder $query) => $query->where('id', Auth::id())))
            )
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
                        fn(Manipulation $record) => Str::of(
                            sprintf(
                                '<ul>%s</ul>',
                                $record->users->map(fn(User $u) => $u?->name ?? '?')
                                    ->map(fn($d) => '<li>' . $d . '</li>')
                                    ->join('')
                            )
                        )->sanitizeHtml()->toHtmlString()
                    )
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: Auth::user()->hasRole('manipulation_manager')),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('attributes.name'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('duration')
                    ->label(__('attributes.duration'))
                    ->formatStateUsing(
                        fn(Manipulation $record): string => $record->duration . ' min'
                    )
                    ->sortable(),
                Tables\Columns\TextColumn::make('slots_count')
                    ->getStateUsing(fn(Manipulation $record) => !$record->archived ? $record->slots->count() : $record->statistics->pluck('slot_count')->sum())
                    ->label(__('attributes.slot_count'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('slots_booked')
                    ->getStateUsing(fn(Manipulation $record) => !$record->archived ? $record->bookings->count() : $record->statistics->pluck('booking_made')->sum())
                    ->label(__('attributes.booked_slots'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('slots_honored')
                    ->getStateUsing(fn(Manipulation $record) => !$record->archived
                        ? $record->bookings->filter(fn(Booking $booking) => $booking->honored)->count()
                        : $record->statistics->map(fn(ManipulationStatistics $stat) => $stat->booking_confirmed_honored + $stat->booking_unconfirmed_honored)->sum())
                    ->label(__('attributes.honored_slots'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('slots_absent')
                    ->getStateUsing(fn(Manipulation $record) => !$record->archived
                        ? $record->bookings->filter(fn(Booking $booking) => !$booking->honored)->count()
                        : $record->statistics->map(fn(ManipulationStatistics $stat) => $stat->booking_made - $stat->booking_confirmed - $stat->booking_unconfirmed)->sum())
                    ->label(__('attributes.absent_slots'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('start_date')
                    ->label(__('attributes.start_date'))
                    ->sortable()
                    ->date('d/m/Y'),
                Tables\Columns\TextColumn::make('end_date')
                    ->label(__('attributes.end_date'))
                    ->sortable()
                    ->date('d/m/Y'),
                Tables\Columns\IconColumn::make('published')
                    ->label('Publié ?')
                    ->boolean(),
                Tables\Columns\IconColumn::make('archived')
                    ->label('Archivé ?')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->boolean(),
                Tables\Columns\TextColumn::make('requirements_str')
                    ->label(__('attributes.requirements'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(
                        fn(Manipulation $record) => Str::of(
                            sprintf(
                                '<ul class="list-disc">%s</ul>',
                                collect($record->requirements)
                                    ->map(fn($r) => '<li>' . $r . '</li>')
                                    ->join('')
                            )
                        )->sanitizeHtml()->toHtmlString()

                    ),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('attributes.created_at'))
                    ->sortable()
                    ->dateTime('d/m/Y H:i:s')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('attributes.updated_at'))
                    ->sortable()
                    ->dateTime('d/m/Y H:i:s')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters(
                [
                    SelectFilter::make('plateau_id')
                        ->label(__('attributes.plateau'))
                        ->options($plateaux->pluck('name', 'id')->unique()->all()),
                    Filter::make('users')
                        ->hidden(Auth::user()->hasRole('manipulation_manager'))
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
                                    fn(Builder $query): Builder => $query->whereHas(
                                        'users',
                                        fn(Builder $query) => $query->where('id', $data['user_id'])
                                    ),
                                );
                        }),
                    TernaryFilter::make('archived')
                        ->label(__('attributes.archived'))
                        ->nullable()
                        ->placeholder(__('attributes.archived_all'))
                        ->trueLabel(__('attributes.archived_no'))
                        ->falseLabel(__('attributes.archived_yes')),
                    TernaryFilter::make('published')
                        ->label(__('attributes.published'))
                        ->nullable()
                        ->placeholder(__('attributes.published_all'))
                        ->trueLabel(__('attributes.published_no'))
                        ->falseLabel(__('attributes.published_yes')),
                ],
                layout: \Filament\Tables\Enums\FiltersLayout::AboveContent
            )
            ->actions([
                Tables\Actions\Action::make('planning')
                    ->label(__('actions.planning'))
                    ->url(fn(Manipulation $record) => route('filament.admin.resources.manipulations.planning', ['record' => $record]))
                    ->color(Color::Lime)
                    ->icon('fas-calendar')
                    ->hidden(fn(Manipulation $record) => !$record->published || $record->archived || !Auth::user()->can('update', $record))
                    ->disabled(fn(Manipulation $record) => !$record->published || $record->archived || !Auth::user()->can('update', $record)),
                Tables\Actions\Action::make('attendance')
                    ->label(__('actions.attendance'))
                    ->url(fn(Manipulation $record) => Attendance::getUrl(['manipulation' => $record->id]))
                    ->color(Color::Cyan)
                    ->icon('fas-signature')
                    ->hidden(fn(Manipulation $record) => !$record->published || $record->archived || !Auth::user()->can('update', $record))
                    ->disabled(fn(Manipulation $record) => !$record->published || $record->archived || !Auth::user()->can('update', $record)),
                Tables\Actions\Action::make('publish')
                    ->label(__('actions.publish'))
                    ->icon('fas-calendar-check')
                    ->action(fn(Manipulation $record) => $record->togglePublished())
                    ->requiresConfirmation()
                    ->color('success')
                    ->hidden(fn(Manipulation $record) => $record->published || $record->archived || !Auth::user()->can('publish', $record))
                    ->disabled(fn(Manipulation $record) => $record->published || $record->archived || !Auth::user()->can('publish', $record)),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make()
                        ->hidden(fn(Manipulation $record) => $record->archived || !Auth::user()->can('update', $record))
                        ->disabled(fn(Manipulation $record) => $record->archived || !Auth::user()->can('update', $record)),
                    Tables\Actions\Action::make('unpublish')
                        ->label(__('actions.unpublish'))
                        ->icon('fas-calendar-xmark')
                        ->action(fn(Manipulation $record) => $record->togglePublished())
                        ->requiresConfirmation()
                        ->color('warning')
                        ->hidden(fn(Manipulation $record) => !$record->published || $record->archived || !Auth::user()->can('publish', $record))
                        ->disabled(fn(Manipulation $record) => !$record->published || $record->archived || !Auth::user()->can('publish', $record)),
                    Tables\Actions\Action::make('share_public_link')
                        ->label(__('actions.share_public_link'))
                        ->icon('fas-share')
                        ->action(function () {})
                        ->color(Color::Lime)
                        ->modalHeading(fn(Manipulation $record) => __('messages.share_public_link.title', ['name' => $record->name]))
                        ->modalContent(fn(Manipulation $record) => view('filament.resources.manipulation-resource.share-public-link', ['record' => $record]))
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel(__('actions.close')),
                    Tables\Actions\Action::make('delete')
                        ->label(__('actions.delete'))
                        ->icon('fas-trash')
                        ->action(fn(Manipulation $record) => $record->delete())
                        ->requiresConfirmation()
                        ->color('danger')
                        ->hidden(fn(Manipulation $record) => $record->archived || !Auth::user()->can('delete', $record))
                        ->disabled(fn(Manipulation $record) => $record->archived || !Auth::user()->can('delete', $record)),
                    Tables\Actions\Action::make('archive')
                        ->label(__('actions.archive'))
                        ->icon('fas-calendar-check')
                        ->action(fn(Manipulation $record) => $record->archive())
                        ->requiresConfirmation()
                        ->color('danger')
                        ->hidden(fn(Manipulation $record) => $record->archived || !Auth::user()->can('archive', $record))
                        ->disabled(fn(Manipulation $record) => $record->archived || !Auth::user()->can('archive', $record)),
                ]),

            ])
            ->bulkActions([]);
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
            'index'    => Pages\ListManipulations::route('/'),
            'create'   => Pages\CreateManipulation::route('/create'),
            'view'     => Pages\ViewManipulation::route('/{record}'),
            'edit'     => Pages\EditManipulation::route('/{record}/edit'),
            'planning' => Pages\ManipulationPlanning::route('/{record}/planning'),
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
        return static::getModel()::query()->active()->count();
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
