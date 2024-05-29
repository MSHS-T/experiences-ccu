<?php

namespace App\Filament\Pages;

use App\Models\Plateau;
use App\Utils\Statistics;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Pages\Page;
use Filament\Support\Enums\VerticalAlignment;
use Illuminate\Support\Facades\Auth;

class ManipulationStatistics extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon  = 'fas-chart-column';
    protected static ?string $navigationLabel = 'Statistiques';
    protected static ?string $title           = 'Statistiques';
    protected static ?string $navigationGroup = 'Plateforme';
    protected static ?int $navigationSort     = 40;
    protected static string $view = 'filament.pages.manipulation-statistics';

    public ?array $formData = [
        'granularity' => 'month',
        'period'      => null
    ];

    public $statistics = null;

    public function form(Form $form): Form
    {
        return $form
            ->statePath('formData')
            ->columns([
                'default' => 1,
                'sm'      => 4
            ])
            ->schema([
                Select::make('granularity')
                    ->label('Type de période')
                    ->placeholder('Tous')
                    ->options([
                        'month' => 'Mois',
                        'year'  => 'Année'
                    ])
                    ->reactive(),
                Select::make('period')
                    ->label('Période')
                    ->options(fn (Get $get) => $this->getPeriods($get('granularity')))
                    ->placeholder('Toutes'),
                Actions::make([
                    Action::make('submit')
                        ->label('Rechercher')
                        ->submit('computeStatistics')
                        ->color('success')
                        ->icon('fas-magnifying-glass')
                ])->verticalAlignment(VerticalAlignment::End)
            ]);
    }

    public function getPeriods(string $granularity): array
    {
        switch ($granularity) {
            case 'month':
                $months = Statistics::getMonths();
                return array_combine($months, $months);
            case 'year':
                $years = Statistics::getYears();
                return array_combine($years, $years);
        }
    }

    public function computeStatistics()
    {
        $this->statistics = match ($this->formData['granularity']) {
            'month' => Statistics::getMonthlyStatistics($this->formData['period']),
            'year'  => Statistics::getYearlyStatistics($this->formData['period']),
        };
    }

    public static function canAccess(): bool
    {
        return Auth::user()->hasRole('administrator');
    }
}
