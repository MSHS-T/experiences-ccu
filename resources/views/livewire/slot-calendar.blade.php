<div class="h-full">
    <div class="relative divide-y">
        @if ($showPlateaux)
            <div class="w-full flex flex-col lg:flex-row items-center mb-4 space-y-2 lg:space-y-0 space-x-4">
                <div class="w-full grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 items-center gap-2">
                    @foreach ($plateaux as $plateau)
                        <label class="flex items-center gap-2">
                            <x-filament::input.checkbox wire:model="checkedPlateaux.{{ $plateau['id'] }}"
                                wire:change="togglePlateau({{ $plateau['id'] }})" />
                            <span class="w-4 h-4 rounded-lg" style="background-color: {{ $plateau['color'] }}">
                            </span>
                            <span class="text-xs lg:text-base">
                                {{ $plateau['name'] }}
                            </span>
                        </label>
                    @endforeach
                </div>
                <div
                    class="flex flex-row lg:flex-col justify-evenly lg:justify-center items-stretch text-sm shrink-0 gap-2">
                    <x-filament::button size="xs" wire:click="checkAllPlateaux()" class="flex items-center gap-2">
                        Tout cocher
                    </x-filament::button>
                    <x-filament::button size="xs" wire:click="uncheckAllPlateaux()"
                        class="flex items-center gap-2">
                        Tout décocher
                    </x-filament::button>
                </div>
            </div>
        @endif
        <div style="padding-top: 16px;">
            <div class="filament-fullcalendar" wire:ignore x-load
                x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('filament-fullcalendar-alpine', 'saade/filament-fullcalendar') }}"
                x-ignore x-data="fullcalendar({
                    locale: @js(app()->getLocale()),
                    plugins: @js(['timeGrid', 'interaction', 'moment', 'momentTimezone']),
                    schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
                    timeZone: @js(config('app.timezone')),
                    selectable: @json(isset($this->manipulation)),
                    editable: @json(false),
                    config: {
                        allDaySlot: false,
                        nowIndicator: true,
                        height: '80vh',
                        views: {
                            customTimeGrid: {
                                type: 'timeGrid',
                                duration: {
                                    days: (window.innerWidth < 640 ? 1 : (window.innerWidth <= 768 ? 3 : 7))
                                }
                            }
                        },
                        headerToolbar: {
                            'left': 'prev,next',
                            'center': 'title',
                            'right': 'today',
                        },
                        initialView: 'customTimeGrid',
                    },
                    eventDidMount: function(info) {
                        if (info.event.extendedProps.background) {
                            info.el.style.background = info.event.extendedProps.background;
                        }
                        if (info.event.extendedProps.type === 'event') {
                            info.el.style.borderWidth = '3px';
                            info.el.style.boxShadow = 'none';
                        }
                    }
                })">
            </div>
        </div>
    </div>
    <x-filament-actions::modals />
</div>
