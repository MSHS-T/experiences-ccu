<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Arr;

class ManageUsers extends ManageRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->using(function (array $data): User {
                    $roles = Arr::get($data, 'roles', null);
                    $record = static::getModel()::create(Arr::except($data, 'roles'));
                    if (filled($roles)) {
                        $record->syncRoles($roles);
                    }

                    return $record;
                }),
        ];
    }
}
