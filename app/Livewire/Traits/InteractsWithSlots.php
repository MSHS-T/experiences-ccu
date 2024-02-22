<?php

namespace App\Livewire\Traits;

use App\Models\Manipulation;
use App\Models\Slot;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\DeleteAction;
use Filament\Actions\StaticAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Support\Enums\Alignment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Saade\FilamentFullCalendar\Widgets\Concerns\InteractsWithEvents;
use Saade\FilamentFullCalendar\Widgets\Concerns\InteractsWithModalActions;
use Saade\FilamentFullCalendar\Widgets\Concerns\InteractsWithRecords;

trait InteractsWithSlots
{
    use InteractsWithActions,
        InteractsWithForms;
    use InteractsWithEvents {
        onEventClick as protected onEventClickTrait;
    }
    use InteractsWithModalActions,
        InteractsWithRecords;

    /**
     * Triggered when the user clicks an event.
     * @param array $event An Event Object that holds information about the event (date, title, etc).
     * @return void
     */
    public function onEventClick(array $event): void
    {
        // Disable click on background events
        if (array_key_exists('id', $event)) {
            $this->onEventClickTrait($event);
        }
    }

    protected function viewAction(): Action
    {
        return ViewAction::make()
            ->record($this->getRecord())
            ->form(fn ($record) => $this->getFormSchema($record))
            ->modalHeading(__('actions.view_slot'))
            ->modalFooterActions([
                DeleteAction::make('delete')
                    ->label(__('actions.delete_slot'))
                    ->color('danger')
                    ->after(function ($livewire) {
                        $this->closeActionModal();
                        $livewire->refreshRecords();
                    })
                    ->button(),
                StaticAction::make('close')
                    ->label(__('actions.close'))
                    ->close()
                    ->color('gray')
                    ->button()
            ])
            ->modalFooterActionsAlignment(Alignment::Center)
            ->cancelParentActions();
    }

    public function getFormSchema(?Slot $record = null): array
    {
        return array_filter([
            Fieldset::make(__('admin.slot'))
                ->columns([
                    'default' => 1,
                    'sm' => 2,
                ])
                ->schema([
                    TextInput::make('id')
                        ->label('ID')
                        ->required()
                        ->maxLength(255)
                        ->visible(Auth::user()->hasRole('administrator'))
                        ->columnSpanFull(),
                    Select::make('manipulation_id')
                        ->label('Manipulation')
                        ->relationship(
                            name: 'manipulation',
                            titleAttribute: 'name',
                        )
                        ->preload()
                        ->required()
                        ->suffixAction(
                            fn ($state) => FormAction::make('planning')
                                ->icon('fas-calendar-days')
                                ->url(route('filament.admin.resources.manipulations.planning', ['record' => $state]))
                                ->openUrlInNewTab()
                        ),
                    Select::make('plateau_id')
                        ->label('Plateau')
                        ->relationship(
                            name: 'plateau',
                            titleAttribute: 'name'
                        )
                        ->preload()
                        ->required()
                        ->suffixAction(
                            fn ($state) => FormAction::make('planning')
                                ->icon('fas-calendar-days')
                                ->url(route('filament.admin.resources.plateaux.planning', ['record' => $state]))
                                ->openUrlInNewTab()
                        ),
                    DateTimePicker::make('start')
                        ->label(__('attributes.start'))
                        ->required(),
                    DateTimePicker::make('end')
                        ->label(__('attributes.end'))
                        ->required(),
                ]),
            $record !== null ? Fieldset::make(__('admin.booking'))
                ->columns([
                    'default' => 1,
                    'sm' => 2,
                ])
                ->relationship('booking')
                ->schema(fn (?Slot $record) => blank($record?->booking) ? [
                    Placeholder::make(__('admin.no_booking'))
                        ->columnSpanFull()
                        ->view('components.no-booking'),
                ] : [
                    TextInput::make('last_name')
                        ->label(__('attributes.last_name'))
                        ->required()
                        ->maxLength(255),
                    TextInput::make('first_name')
                        ->label(__('attributes.first_name'))
                        ->required()
                        ->maxLength(255),
                    TextInput::make('email')
                        ->label(__('attributes.email'))
                        ->required()
                        ->maxLength(255),
                    TextInput::make('birthday')
                        ->label(__('attributes.birthdate'))
                        ->required()
                        ->maxLength(255),
                    DateTimePicker::make('created_at')
                        ->label(__('attributes.booked_at')),
                    Toggle::make('confirmed')
                        ->label(__('attributes.confirmed'))
                        ->inline(false)
                ])
                : null,
        ]);
    }

    public function getModel(): ?string
    {
        return Slot::class;
    }

    protected function getEloquentQuery(): Builder
    {
        return Slot::query()->with('booking');
    }
}
