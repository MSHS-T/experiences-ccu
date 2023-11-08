<?php

namespace App\Filament\Pages;

use App\Settings\GeneralSettings;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Illuminate\Support\Facades\Auth;

class ManageSettings extends SettingsPage
{
    protected static string $settings         = GeneralSettings::class;
    protected static ?string $navigationIcon  = 'fas-cog';
    protected static ?string $navigationLabel = 'Réglages';
    protected static ?int $navigationSort     = 50;
    protected static ?string $navigationGroup = 'Administration';
    protected ?string $heading                = 'Réglages';

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()->hasRole('administrator');
    }

    public function mount(): void
    {
        abort_unless(Auth::user()->hasRole('administrator'), 403);
        parent::mount();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                RichEditor::make('presentation_text')
                    ->label(__('attributes.presentation_text'))
                    ->disableAllToolbarButtons()
                    ->enableToolbarButtons(['bold', 'italic', 'strike', 'link', 'bulletList', 'orderedList'])
                    ->required(),
                RichEditor::make('access_instructions')
                    ->label(__('attributes.access_instructions'))
                    ->disableAllToolbarButtons()
                    ->enableToolbarButtons(['bold', 'italic', 'strike', 'link', 'bulletList', 'orderedList'])
                    ->required(),
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
                TextInput::make('email_reminder_delay')
                    ->label(__('attributes.email_reminder_delay'))
                    ->integer()
                    ->minValue(0)
                    ->suffix('jours')
                    ->required(),
            ]);
    }
}
