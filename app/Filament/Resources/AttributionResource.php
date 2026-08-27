<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\CheckboxList;
use Filament\Actions\Action;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\AttributionResource\Pages\ListAttributions;
use App\Filament\Resources\AttributionResource\Pages\CreateAttribution;
use App\Filament\Resources\AttributionResource\Pages\EditAttribution;
use App\Filament\Resources\AttributionResource\Pages;
use App\Models\Attribution;
use App\Models\Plateau;
use App\Models\User;
use Filament\Forms;
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

    protected static string | \BackedEnum | null $navigationIcon   = 'fas-calendar-check';
    protected static ?string $navigationLabel  = 'Attributions';
    protected static ?int $navigationSort      = 20;
    protected static string | \UnitEnum | null $navigationGroup  = 'Plateforme';
    protected static ?string $modelLabel       = 'Attribution';
    protected static ?string $pluralModelLabel = 'Attributions';

    public static function form(Schema $schema): Schema
    {
        $user = Auth::user();
        $plateaux = $user->hasRole('administrator') ? Plateau::all() : $user->plateaux;
        $halfdays = collect(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'])
            ->crossJoin(['am', 'pm'])
            ->mapWithKeys(fn($item) => [
                $item[0] . '_' . $item[1] => __('attributes.' . $item[0]) . ' ' . __('attributes.' . $item[1])
            ])
            ->all();

        return $schema
            ->components([
                Select::make('plateau_id')
                    ->label(__('attributes.plateau'))
                    ->relationship('plateau', 'name')
                    ->options($plateaux->pluck('name', 'id'))
                    ->required(),
                Select::make('manipulation_manager_id')
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
                DatePicker::make('start_date')
                    ->label(__('attributes.start_date'))
                    ->required(),
                DatePicker::make('end_date')
                    ->label(__('attributes.end_date'))
                    ->after('start_date')
                    ->required(),
                CheckboxList::make('allowed_halfdays')
                    ->label(__('attributes.allowed_halfdays'))
                    ->helperText(__('messages.allowed_halfdays_help'))
                    ->hintActions([
                        Action::make('checkAll')
                            ->label('Tout cocher')
                            ->icon('fas-plus')
                            ->action(fn(Set $set) => $set('allowed_halfdays', array_keys($halfdays))),
                        Action::make('uncheckAll')
                            ->label('Tout décocher')
                            ->icon('fas-minus')
                            ->action(fn(Set $set) => $set('allowed_halfdays', []))
                    ])
                    ->options(
                        $halfdays
                    )
                    ->columns([
                        'default' => 2,
                        'md'      => 3,
                        'lg'      => 5,
                        'xl' => 7
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
                    return $user->attributions->some(fn(Attribution $attribution) => $attribution->plateau->id === $plateau->id);
                }
            });
        return $table
            ->modifyQueryUsing(
                fn(Builder $query) =>
                $query->whereIn('plateau_id', $plateaux->pluck('id'))
                    ->when(Auth::user()->hasRole('manipulation_manager'), fn(Builder $query) => $query->where('manipulation_manager_id', Auth::id()))
            )
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable(),
                TextColumn::make('plateau.name'),
                TextColumn::make('manipulation_manager_id')
                    ->label(__('attributes.manipulation_manager'))
                    ->formatStateUsing(
                        fn(Attribution $record): string => $record->manipulationManager?->name ?? '?'
                    )
                    ->sortable(),
                TextColumn::make('creator_id')
                    ->label(__('attributes.creator'))
                    ->formatStateUsing(
                        fn(Attribution $record): string => $record->creator?->name ?? '?'
                    )
                    ->sortable(),
                TextColumn::make('start_date')
                    ->label(__('attributes.start_date'))
                    ->sortable()
                    ->date('d/m/Y'),
                TextColumn::make('end_date')
                    ->label(__('attributes.end_date'))
                    ->sortable()
                    ->date('d/m/Y'),
                TextColumn::make('allowed_halfdays')
                    ->label(__('attributes.allowed_halfdays'))
                    ->formatStateUsing(
                        fn(Attribution $record) => Str::of(
                            sprintf(
                                '<ul class="list-disc">%s</ul>',
                                collect($record->getSimplifiedAllowedHalfdaysDisplay())
                                    ->map(fn($r) => '<li>' . $r . '</li>')
                                    ->join('')
                            )
                        )->sanitizeHtml()->toHtmlString()
                    ),
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
                    SelectFilter::make('plateau_id')
                        ->label(__('attributes.plateau'))
                        ->options(
                            $plateaux->pluck('name', 'id')->unique()->all()
                        ),
                    SelectFilter::make('manipulation_manager_id')
                        ->label(__('attributes.manipulation_manager'))
                        ->hidden(Auth::user()->hasRole('manipulation_manager'))
                        ->options(
                            User::role('manipulation_manager')
                                ->get()
                                ->pluck('name', 'id')
                                ->all()
                        )
                ],
                layout: FiltersLayout::AboveContent
            )
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
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
            'index' => ListAttributions::route('/'),
            'create' => CreateAttribution::route('/create'),
            'edit' => EditAttribution::route('/{record}/edit'),
        ];
    }
}
