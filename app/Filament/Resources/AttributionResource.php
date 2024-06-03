<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttributionResource\Pages;
use App\Models\Attribution;
use App\Models\Plateau;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AttributionResource extends Resource
{
    protected static ?string $model = Attribution::class;

    protected static ?string $navigationIcon   = 'fas-calendar-check';
    protected static ?string $navigationLabel  = 'Attributions';
    protected static ?int $navigationSort      = 20;
    protected static ?string $navigationGroup  = 'Plateforme';
    protected static ?string $modelLabel       = 'Attribution';
    protected static ?string $pluralModelLabel = 'Attributions';

    public static function form(Form $form): Form
    {
        $user = Auth::user();
        $plateaux = $user->hasRole('administrator') ? Plateau::all() : $user->plateaux;
        $halfdays = collect(['monday', 'tuesday', 'wednesday', 'thursday', 'friday'])
            ->crossJoin(['am', 'pm'])
            ->mapWithKeys(fn ($item) => [
                $item[0] . '_' . $item[1] => __('attributes.' . $item[0]) . ' ' . __('attributes.' . $item[1])
            ])
            ->all();

        return $form
            ->schema([
                Forms\Components\Select::make('plateau_id')
                    ->label(__('attributes.plateau'))
                    ->relationship('plateau', 'name')
                    ->options($plateaux->pluck('name', 'id'))
                    ->required(),
                Forms\Components\Select::make('manipulation_manager_id')
                    ->label(__('attributes.manipulation_manager'))
                    ->options(
                        User::role('manipulation_manager')
                            ->get()
                            ->pluck('name', 'id')
                            ->all()
                    )
                    ->preload()
                    ->searchable()
                    ->required(),
                Forms\Components\DatePicker::make('start_date')
                    ->label(__('attributes.start_date'))
                    ->required(),
                Forms\Components\DatePicker::make('end_date')
                    ->label(__('attributes.end_date'))
                    ->after('start_date')
                    ->required(),
                Forms\Components\CheckboxList::make('allowed_halfdays')
                    ->label(__('attributes.allowed_halfdays'))
                    ->helperText(__('messages.allowed_halfdays_help'))
                    ->hintActions([
                        Action::make('checkAll')
                            ->label('Tout cocher')
                            ->icon('fas-plus')
                            ->action(fn (Set $set) => $set('allowed_halfdays', array_keys($halfdays))),
                        Action::make('uncheckAll')
                            ->label('Tout décocher')
                            ->icon('fas-minus')
                            ->action(fn (Set $set) => $set('allowed_halfdays', []))
                    ])
                    ->options(
                        $halfdays
                    )
                    ->columns([
                        'default' => 2,
                        'md'      => 3,
                        'xl'      => 5
                    ])
                    ->columnSpanFull()
                    ->required()
                    ->filled(),
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
                    return $user->attributions->some(fn (Attribution $attribution) => $attribution->plateau->id === $plateau->id);
                }
            });
        return $table
            ->modifyQueryUsing(
                fn (Builder $query) =>
                $query->whereIn('plateau_id', $plateaux->pluck('id'))
                    ->when(Auth::user()->hasRole('manipulation_manager'), fn (Builder $query) => $query->where('manipulation_manager_id', Auth::id()))
            )
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable(),
                Tables\Columns\TextColumn::make('plateau.name'),
                Tables\Columns\TextColumn::make('manipulation_manager_id')
                    ->label(__('attributes.manipulation_manager'))
                    ->formatStateUsing(
                        fn (Attribution $record): string => $record->manipulationManager?->name ?? '?'
                    )
                    ->sortable(),
                Tables\Columns\TextColumn::make('creator_id')
                    ->label(__('attributes.creator'))
                    ->formatStateUsing(
                        fn (Attribution $record): string => $record->creator?->name ?? '?'
                    )
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->label(__('attributes.start_date'))
                    ->sortable()
                    ->date('d/m/Y'),
                Tables\Columns\TextColumn::make('end_date')
                    ->label(__('attributes.end_date'))
                    ->sortable()
                    ->date('d/m/Y'),
                Tables\Columns\TextColumn::make('allowed_halfdays')
                    ->label(__('attributes.allowed_halfdays'))
                    ->formatStateUsing(
                        fn (Attribution $record) => Str::of(
                            sprintf(
                                '<ul class="list-disc">%s</ul>',
                                collect($record->getSimplifiedAllowedHalfdaysDisplay())
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
                            $plateaux->pluck('name', 'id')->unique()->all()
                        ),
                    SelectFilter::make('manipulation_manager_id')
                        ->label(__('attributes.manipulation_manager'))
                        ->hidden(Auth::user()->hasRole('manipulation_manager'))
                        ->options(
                            User::role('plateau_manager')
                                ->get()
                                ->pluck('name', 'id')
                                ->all()
                        )
                ],
                layout: FiltersLayout::AboveContent
            )
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListAttributions::route('/'),
            'create' => Pages\CreateAttribution::route('/create'),
            'edit' => Pages\EditAttribution::route('/{record}/edit'),
        ];
    }
}
