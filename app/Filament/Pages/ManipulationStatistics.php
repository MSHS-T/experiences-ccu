<?php

namespace App\Filament\Pages;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Actions;
use Filament\Actions\Action;
use App\Models\Plateau;
use App\Utils\Statistics;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Support\Enums\VerticalAlignment;
use Illuminate\Support\Facades\Auth;

class ManipulationStatistics extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon  = 'fas-chart-column';
    protected static ?string $navigationLabel = 'Statistiques';
    protected static ?string $title           = 'Statistiques';
    protected static string | \UnitEnum | null $navigationGroup = 'Plateforme';
    protected static ?int $navigationSort     = 40;
    protected string $view = 'filament.pages.manipulation-statistics';

    public ?array $formData = [
        'type'        => null,
        'granularity' => null,
        'period'      => null
    ];

    public $statistics = null;
    public $type = null;
    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('formData')
            ->columns([
                'default' => 1,
                'sm'      => 4
            ])
            ->schema([
                Select::make('type')
                    ->label('Type de statistiques')
                    ->options([
                        'plateau' => 'Plateau',
                        'manager' => 'Responsable Manipulation'
                    ]),
                Select::make('granularity')
                    ->label('Type de période')
                    ->options([
                        'month' => 'Mois',
                        'year'  => 'Année'
                    ])
                    ->reactive(),
                Select::make('period')
                    ->label('Période')
                    ->options(fn(Get $get) => $this->getPeriods($get('granularity')))
                    ->placeholder('Toutes'),
                Actions::make([
                    Action::make('submit')
                        ->label('Rechercher')
                        ->submit('computeStatistics')
                        ->color('success')
                        ->icon('fas-magnifying-glass')
                        ->disabled(fn(Get $get) => empty($get('granularity')) || empty($get('type'))),
                ])->verticalAlignment(VerticalAlignment::End)
            ]);
    }

    public function getPeriods(string|null $granularity): array
    {
        switch ($granularity) {
            case 'month':
                $months = array_filter(Statistics::getMonths());
                return array_combine($months, $months);
            case 'year':
                $years = array_filter(Statistics::getYears());
                return array_combine($years, $years);
            default:
                return [];
        }
    }

    public function computeStatistics()
    {
        $this->statistics = match ([$this->formData['type'], $this->formData['granularity']]) {
            ['plateau', 'month'] => Statistics::getPlateauMonthlyStatistics($this->formData['period']),
            ['plateau', 'year']  => Statistics::getPlateauYearlyStatistics($this->formData['period']),
            ['manager', 'month'] => Statistics::getManagerMonthlyStatistics($this->formData['period']),
            ['manager', 'year']  => Statistics::getManagerYearlyStatistics($this->formData['period']),
            default => null,
        };
        $this->type = $this->formData['type'];
    }

    public static function canAccess(): bool
    {
        return Auth::user()->hasRole('administrator');
    }
}
