<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Support\Arr;
use Spatie\Permission\Models\Role;
use STS\FilamentImpersonate\Tables\Actions\Impersonate;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon   = 'fas-users';
    protected static ?string $navigationLabel  = 'Utilisateurs';
    protected static ?int $navigationSort      = 40;
    protected static ?string $navigationGroup  = 'Administration';
    protected static ?string $modelLabel       = 'Utilisateur';
    protected static ?string $pluralModelLabel = 'Utilisateurs';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('first_name')
                    ->label(__('attributes.first_name'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('last_name')
                    ->label(__('attributes.last_name'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->label(__('attributes.email'))
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\Select::make('roles')
                    ->label(__('attributes.role'))
                    ->relationship('roles', 'name')
                    ->getOptionLabelFromRecordUsing(fn (Role $record) => __('attributes.roles.' . $record->name))
                    ->preload()
                    ->multiple()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('first_name')
                    ->label(__('attributes.first_name'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->label(__('attributes.last_name'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label(__('attributes.email'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label(__('attributes.role'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'administrator'        => 'danger',
                        'plateau_manager'      => 'warning',
                        'manipulation_manager' => 'primary',
                    })
                    ->formatStateUsing(fn (string $state) => __('attributes.roles.' . $state))
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
            ->filters([
                //
            ])
            ->actions([
                Impersonate::make()->redirectTo(route('filament.admin.pages.dashboard')),
                Tables\Actions\EditAction::make()
                    ->using(function (User $record, array $data): User {
                        $role = Arr::get($data, 'role', null);
                        $record->update(Arr::except($data, 'role'));
                        if ($role !== null) {
                            $record->syncRoles($role);
                        }

                        return $record;
                    }),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageUsers::route('/'),
        ];
    }
}
