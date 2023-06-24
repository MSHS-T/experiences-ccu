<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Arr;

class ManageUsers extends ManageRecords
{
    protected static string $resource = UserResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->using(function (array $data): User {
                    $role = Arr::get($data, 'role', null);
                    $record = static::getModel()::create(Arr::except($data, 'role'));
                    if ($role !== null) {
                        $record->syncRoles($role);
                    }

                    return $record;
                }),
        ];
    }
}
