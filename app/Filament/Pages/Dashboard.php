<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\CalendarWidget;
use App\Filament\Widgets\DashboardCalendarWidget;
use Filament\Pages\Dashboard as BasePage;
use Filament\Widgets;
use Illuminate\Contracts\Support\Htmlable;

class Dashboard extends BasePage
{
    protected static string | \BackedEnum | null $navigationIcon = 'fas-home';

    public function getColumns(): int|array
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
            // Widgets\AccountWidget::class,
            // CalendarWidget::class,
            DashboardCalendarWidget::class
        ];
    }
}
