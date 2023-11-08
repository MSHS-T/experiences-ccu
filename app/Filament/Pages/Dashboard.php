<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\CalendarWidget;
use Filament\Pages\Dashboard as BasePage;
use Filament\Widgets;

class Dashboard extends BasePage
{
    protected static ?string $navigationIcon = 'fas-home';

    public function getColumns(): int | string | array
    {
        return [
            'default' => 1,
            'sm'      => 2,
            'lg'      => 3
        ];
    }
    public function getWidgets(): array
    {
        return [
            Widgets\AccountWidget::class,
            CalendarWidget::class,
        ];
    }
}
