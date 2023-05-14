<?php

namespace App\Http\Livewire;

use Filament\Forms\Components\TextInput;
use JeffGreco13\FilamentBreezy\Pages\MyProfile as BreezyMyProfile;
use Livewire\Component;

class MyProfile extends BreezyMyProfile
{
    protected function getUpdateProfileFormSchema(): array
    {
        return [
            TextInput::make('last_name')
                ->required()
                ->label(__('attributes.last_name')),
            TextInput::make('first_name')
                ->required()
                ->label(__('attributes.first_name')),
            TextInput::make($this->loginColumn)
                ->required()
                ->email(fn () => $this->loginColumn === 'email')
                ->unique(config('filament-breezy.user_model'), ignorable: $this->user)
                ->label(__('attributes.email')),
        ];
    }
}
