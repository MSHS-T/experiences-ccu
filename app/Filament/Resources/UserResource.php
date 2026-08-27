<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use App\Filament\Resources\UserResource\Pages\ManageUsers;
use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Illuminate\Support\Arr;
use Spatie\Permission\Models\Role;
use STS\FilamentImpersonate\Actions\Impersonate;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string | \BackedEnum | null $navigationIcon   = 'fas-users';
    protected static ?string $navigationLabel  = 'Utilisateurs';
    protected static ?int $navigationSort      = 40;
    protected static string | \UnitEnum | null $navigationGroup  = 'Administration';
    protected static ?string $modelLabel       = 'Utilisateur';
    protected static ?string $pluralModelLabel = 'Utilisateurs';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('first_name')
                    ->label(__('attributes.first_name'))
                    ->required()
                    ->maxLength(255),
                TextInput::make('last_name')
                    ->label(__('attributes.last_name'))
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label(__('attributes.email'))
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Select::make('roles')
                    ->label(__('attributes.role'))
                    ->relationship('roles', 'name')
                    ->getOptionLabelFromRecordUsing(fn(Role $record) => __('attributes.roles.' . $record->name))
                    ->preload()
                    ->multiple()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('first_name')
                    ->label(__('attributes.first_name'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('last_name')
                    ->label(__('attributes.last_name'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('email')
                    ->label(__('attributes.email'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('roles.name')
                    ->label(__('attributes.role'))
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'administrator'        => 'danger',
                        'plateau_manager'      => 'warning',
                        'manipulation_manager' => 'primary',
                    })
                    ->formatStateUsing(fn(string $state) => __('attributes.roles.' . $state))
                    ->sortable(),
                IconColumn::make('email_verified_at')
                    ->label(__('attributes.email_verified_at'))
                    ->sortable()
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label(__('attributes.created_at'))
                    ->sortable()
                    ->dateTime('d/m/Y H:i:s'),
                TextColumn::make('updated_at')
                    ->label(__('attributes.updated_at'))
                    ->sortable()
                    ->dateTime('d/m/Y H:i:s'),
            ])
            ->filters([
                TrashedFilter::make(),
            ], layout: FiltersLayout::AboveContent)
            ->recordActions([
                Impersonate::make()
                    ->redirectTo(route('filament.admin.pages.dashboard')),
                EditAction::make()
                    ->using(function (User $record, array $data): User {
                        $role = Arr::get($data, 'role', null);
                        $record->update(Arr::except($data, 'role'));
                        if ($role !== null) {
                            $record->syncRoles($role);
                        }

                        return $record;
                    }),
                DeleteAction::make()
                    ->action(function (User $record) {
                        if ($record->hasRole('manipulation_manager') && $record->manipulations()->active()->count() > 0) {
                            Notification::make()
                                ->title('Erreur')
                                ->body('Impossible de supprimer un Responsable Manipulation associé à des expériences en cours.')
                                ->danger()
                                ->send();
                            return;
                        }
                        $record->delete();
                    }),
                ForceDeleteAction::make(),
                RestoreAction::make(),
            ])
            ->toolbarActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageUsers::route('/'),
        ];
    }
}
