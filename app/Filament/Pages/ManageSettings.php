<?php

namespace App\Filament\Pages;

use App\Settings\GeneralSettings;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Actions\Action;
use Filament\Pages\SettingsPage;

class ManageSettings extends SettingsPage
{
    protected static string $settings         = GeneralSettings::class;
    protected static ?string $navigationIcon  = 'fas-cog';
    protected static ?string $navigationLabel = 'Réglages';
    protected static ?int $navigationSort     = 50;
    protected static ?string $navigationGroup = 'Administration';
    protected ?string $heading                = 'Réglages';

    protected function getFormSchema(): array
    {
        return [
            Grid::make()
                ->maxWidth('lg')
                ->columns(1)
                ->schema([
                    TextInput::make('booking_cancellation_delay')
                        ->label(__('attributes.booking_cancellation_delay'))
                        ->integer()
                        ->minValue(0)
                        ->suffix('jours')
                        ->required(),
                    TextInput::make('booking_confirmation_delay')
                        ->label(__('attributes.booking_confirmation_delay'))
                        ->integer()
                        ->minValue(0)
                        ->suffix('jours')
                        ->required(),
                    TextInput::make('booking_opening_delay')
                        ->label(__('attributes.booking_opening_delay'))
                        ->integer()
                        ->minValue(0)
                        ->suffix('jours')
                        ->required(),
                    TextInput::make('manipulation_overbooking')
                        ->label(__('attributes.manipulation_overbooking'))
                        ->integer()
                        ->minValue(0)
                        ->suffix('%')
                        ->required(),
                    TextInput::make('email_reminder_delay')
                        ->label(__('attributes.email_reminder_delay'))
                        ->integer()
                        ->minValue(0)
                        ->suffix('jours')
                        ->required(),
                    RichEditor::make('presentation_text')
                        ->label(__('attributes.presentation_text'))
                        ->disableAllToolbarButtons()
                        ->enableToolbarButtons(['bold', 'italic', 'strike', 'link', 'bulletList', 'orderedList'])
                        ->required(),
                    RichEditor::make('access_instructions')
                        ->label(__('attributes.access_instructions'))
                        ->disableAllToolbarButtons()
                        ->enableToolbarButtons(['bold', 'italic', 'strike', 'link', 'bulletList', 'orderedList'])
                        ->required()
                ])
        ];
    }

    protected function getSaveFormAction(): Action
    {
        return Action::make('save')
            ->label(__('filament::resources/pages/edit-record.form.actions.save.label'))
            ->submit('save')
            ->keyBindings(['mod+s']);
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return __('filament-support::actions/edit.single.messages.saved');
    }
}
