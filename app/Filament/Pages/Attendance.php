<?php

namespace App\Filament\Pages;

use App\Models\Booking;
use App\Models\Manipulation;
use App\Models\Slot;
use App\Models\SlotBooking;
use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Support\Colors\Color;
use Filament\Tables\Actions\Action as TableAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Reactive;
use Livewire\Attributes\Url;

class Attendance extends Page implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'fas-signature';
    protected static string $view = 'filament.pages.attendance';

    #[Url(as: 'date', history: true)]
    public ?string $queryDate;
    public Carbon $date;

    #[Url(as: 'manipulation', history: true)]
    public ?int $manipulationId;

    public $manipulations = [];
    public $slots = [];

    public static function getNavigationLabel(): string
    {
        return __('actions.attendance');
    }

    public function getTitle(): string | Htmlable
    {
        return __('actions.attendance') . ' - ' . $this->date?->format('d/m/Y');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('prevDay')
                ->label(__('admin.attendance.prevDay'))
                ->hiddenLabel()
                ->action(fn() => $this->setDate($this->date->clone()->subDay()))
                ->icon('fas-chevron-left'),
            Action::make('today')
                ->label(__('admin.attendance.today'))
                ->action(fn() => $this->setDate(today())),
            Action::make('nextDay')
                ->label(__('admin.attendance.nextDay'))
                ->hiddenLabel()
                ->action(fn() => $this->setDate($this->date->clone()->addDay()))
                ->icon('fas-chevron-right')
                ->disabled(fn() => $this->date->isFuture()),
        ];
    }

    public function setDate(Carbon $date): void
    {
        $this->date = $date;
        $this->queryDate = $date->format('Ymd');
        $this->manipulations = $this->getManipulations();
        if (isset($this->manipulationId) && filled($this->manipulationId) && !array_key_exists($this->manipulationId, $this->manipulations)) {
            $this->manipulationId = null;
        }
        $this->resetTable();
    }

    public function mount(): void
    {
        if (isset($this->queryDate) && filled($this->queryDate)) {
            $this->setDate(Carbon::createFromFormat('Ymd', $this->queryDate));
        } else {
            $this->setDate(today());
        }
    }

    public function setManipulation(int $manipulationId): void
    {
        $this->manipulationId = $manipulationId;
        $this->resetTable();
    }

    public function getManipulations(): array
    {
        $user = Auth::user();
        return Manipulation::where('start_date', '<=', $this->date)
            ->where('end_date', '>=', $this->date)
            ->where('published', 1)
            ->where('archived', 0)
            ->get()
            ->filter(fn(Manipulation $m) => match (true) {
                $user->hasAnyRole('administrator')        => true,
                $user->hasAnyRole('plateau_manager')      => $m->plateau->manager_id === $user->id,
                $user->hasAnyRole('manipulation_manager') => $m->users->some(fn($u) => $u->id === $user->id),
                default                                   => false
            })
            ->pluck('name', 'id')
            ->toArray();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                SlotBooking::query()
                    ->where('manipulation_id', isset($this->manipulationId) ? $this->manipulationId : null)
                    ->where('start', '>=', $this->date)
                    ->where('end', '<=', $this->date->clone()->endOfDay())
                    ->orderBy('start')
                    ->orderBy('last_name')
                    ->orderBy('first_name')
            )
            ->columns([
                // TextColumn::make('id'),
                TextColumn::make('last_name')
                    ->label(__('attributes.last_name'))
                    ->getStateUsing(fn($record) => $record->last_name === null ? '∅' : $record->last_name),
                TextColumn::make('first_name')
                    ->label(__('attributes.first_name')),
                TextColumn::make('email')
                    ->label(__('attributes.email')),
                IconColumn::make('confirmed')
                    ->label(__('attributes.confirmed'))
                    ->boolean(),
                // TextColumn::make('start')
                //     ->label(__('attributes.start'))
                //     ->time('H:i'),
                // TextColumn::make('end')
                //     ->label(__('attributes.end'))
                //     ->time('H:i'),
            ])
            ->defaultGroup(
                Group::make('start')
                    ->label('')
                    ->getTitleFromRecordUsing(fn($record) => Carbon::parse($record['start'])->format('H:i') . ' - ' . Carbon::parse($record['end'])->format('H:i')),
            )
            ->actions([
                TableAction::make('markHonored')
                    ->label(__('actions.mark_honored'))
                    ->hidden(fn(SlotBooking $record) => blank($record->booking_id))
                    ->disabled(fn(SlotBooking $record) => Carbon::parse($record->start)->startOfDay()->isAfter(now()->startOfDay()))
                    ->color(fn(SlotBooking $record) => $record->honored === 1 ? Color::Green : Color::Gray)
                    ->icon(fn(SlotBooking $record) => $record->honored === 1 ? 'fas-check' : null)
                    ->action(function (SlotBooking $record) {
                        if (boolval($record->honored) !== true) {
                            $record->honored = 1;
                            Booking::find($record->booking_id)->update(['honored' => true]);
                        } else {
                            $record->honored = null;
                            Booking::find($record->booking_id)->update(['honored' => null]);
                        }
                    }),
                TableAction::make('markNotHonored')
                    ->label(__('actions.mark_not_honored'))
                    ->hidden(fn(SlotBooking $record) => blank($record->booking_id))
                    ->disabled(fn(SlotBooking $record) => Carbon::parse($record->start)->startOfDay()->isAfter(now()->startOfDay()))
                    ->color(fn(SlotBooking $record) => $record->honored === 0 ? Color::Red : Color::Gray)
                    ->icon(fn(SlotBooking $record) => $record->honored === 0 ? 'fas-check' : null)
                    ->action(function (SlotBooking $record) {
                        if (boolval($record->honored) !== false) {
                            $record->honored = 0;
                            Booking::find($record->booking_id)->update(['honored' => false]);
                        } else {
                            $record->honored = null;
                            Booking::find($record->booking_id)->update(['honored' => null]);
                        }
                    }),
            ])
            ->paginated(false)
            ->striped(false)
            ->emptyStateHeading('Aucun créneau trouvé')
            ->emptyStateDescription('Vérifiez votre choix de manipulation et de date.');
    }
}
