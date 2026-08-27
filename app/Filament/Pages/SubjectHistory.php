<?php

namespace App\Filament\Pages;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Actions;
use Filament\Actions\Action;
use App\Utils\Subject;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Support\Enums\VerticalAlignment;
use Illuminate\Support\Facades\Auth;

class SubjectHistory extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon  = 'fas-magnifying-glass';
    protected static ?string $navigationLabel = 'Recherche de participant';
    protected static ?string $title           = 'Recherche de participant';
    protected static string | \UnitEnum | null $navigationGroup = 'Plateforme';
    protected static ?int $navigationSort     = 30;
    protected string $view             = 'filament.pages.subject-history';

    public ?array $formData = [
        'email' => null,
    ];

    public ?array $subjectHistory;
    public bool $notFound = false;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('formData')
            ->columns([
                'default' => 1,
                'sm'      => 4
            ])
            ->schema([
                TextInput::make('email')
                    ->label('Email du participant')
                    ->inlineLabel()
                    ->email()
                    ->columnSpan([
                        'default' => 1,
                        'sm'      => 3,
                    ]),
                Actions::make([
                    Action::make('submit')
                        ->label('Rechercher')
                        ->submit('searchSubject')
                        ->color('success')
                        ->icon('fas-magnifying-glass')
                ])->verticalAlignment(VerticalAlignment::End)
            ]);
    }

    public function searchSubject()
    {
        $this->notFound = false;
        if (filled($this->formData['email'])) {
            $subject = Subject::find($this->formData['email']);
            if (blank($subject)) {
                $this->notFound = true;
            } else {
                $this->subjectHistory = $subject->history(withPercentage: true);
            }
        }
    }

    public function toggleBlock()
    {
        if ($this->subjectHistory) {
            $subject = Subject::find($this->formData['email']);
            ($this->subjectHistory['blocked']) ? $subject->unblock() : $subject->block();
            $this->subjectHistory = $subject->history(withPercentage: true);
        }
    }

    public static function canAccess(): bool
    {
        return Auth::user()->hasRole('administrator');
    }
}
