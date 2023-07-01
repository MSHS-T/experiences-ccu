<?php

namespace App\Filament\Resources\AttributionResource\Pages;

use App\Filament\Resources\AttributionResource;
use Filament\Notifications\Notification;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAttribution extends CreateRecord
{
    protected static string $resource = AttributionResource::class;
}
