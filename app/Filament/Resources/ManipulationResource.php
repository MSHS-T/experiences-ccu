<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ManipulationResource\Pages;
use App\Filament\Resources\ManipulationResource\RelationManagers;
use App\Models\Manipulation;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ManipulationResource extends Resource
{
    protected static ?string $model = Manipulation::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('plateau_id')
                    ->relationship('plateau', 'name')
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->maxLength(65535),
                Forms\Components\TextInput::make('duration')
                    ->required(),
                Forms\Components\TextInput::make('target_slots')
                    ->required(),
                Forms\Components\DatePicker::make('start_date')
                    ->required(),
                Forms\Components\TextInput::make('location')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('available_hours')
                    ->required(),
                Forms\Components\TextInput::make('requirements')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('plateau.name'),
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('description'),
                Tables\Columns\TextColumn::make('duration'),
                Tables\Columns\TextColumn::make('target_slots'),
                Tables\Columns\TextColumn::make('start_date')
                    ->date(),
                Tables\Columns\TextColumn::make('location'),
                Tables\Columns\TextColumn::make('available_hours'),
                Tables\Columns\TextColumn::make('requirements'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
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
            //
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListManipulations::route('/'),
            'create' => Pages\CreateManipulation::route('/create'),
            'view' => Pages\ViewManipulation::route('/{record}'),
            'edit' => Pages\EditManipulation::route('/{record}/edit'),
        ];
    }    
}
