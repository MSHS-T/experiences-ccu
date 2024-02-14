<div class="h-full">
    <div class="relative divide-y">
        <div class="w-full flex flex-wrap justify-between flex-1 mb-4">
            @foreach ($plateaux as $plateau)
                <label class="flex items-center gap-2">
                    <x-filament::input.checkbox wire:model="checkedPlateaux.{{ $plateau['id'] }}"
                        wire:change="togglePlateau({{ $plateau['id'] }})" />
                    <span class="w-4 h-4 rounded-lg" style="background-color: {{ $plateau['color'] }}">
                    </span>
                    <span>
                        {{ $plateau['name'] }}
                    </span>
                </label>
            @endforeach
        </div>
        <div style="padding-top: 16px;">
            <div class="filament-fullcalendar" wire:ignore ax-load
                ax-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('filament-fullcalendar-alpine', 'saade/filament-fullcalendar') }}"
                ax-load-css="{{ \Filament\Support\Facades\FilamentAsset::getStyleHref('filament-fullcalendar-styles', 'saade/filament-fullcalendar') }}"
                x-ignore x-data="fullcalendar({
                    locale: @js(app()->getLocale()),
                    plugins: @js(['timeGrid', 'interaction', 'moment', 'momentTimezone']),
                    schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
                    timeZone: @js(config('app.timezone')),
                    selectable: @json(false),
                    editable: @json(false),
                    config: {
                        allDaySlot: false,
                        height: '80vh',
                        views: {
                            customTimeGrid: {
                                type: 'timeGrid',
                                duration: {
                                    days: (window.innerWidth > 500 ? 4 : 2)
                                }
                            }
                        },
                        headerToolbar: {
                            'left': 'prev,next',
                            'center': 'title',
                            'right': 'today',
                        },
                        initialView: 'customTimeGrid',
                        eventDidMount: function(info) {
                            if (info.event.extendedProps.background) {
                                info.el.style.background = info.event.extendedProps.background;
                            }
                        }
                    }
                })">
            </div>
        </div>
    </div>
    <x-filament-actions::modals />
</div>
