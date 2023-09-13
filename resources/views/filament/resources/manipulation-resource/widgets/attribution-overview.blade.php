<x-filament-widgets::widget>
    <x-filament::section icon="fas-calendar-check" collapsible compact>
        <x-slot name="heading">
            {{ __('attributes.attributions') }}
        </x-slot>

        {{-- Content --}}
        <div class="flex flex-col md:flex-row flex-wrap items-stretch gap-2">
            @foreach ($this->attributionList as $plateau => $attributions)
                <x-filament::section compact collapsible>
                    <x-slot name="heading">
                        {{ $plateau }}
                    </x-slot>
                    <x-slot name="headerEnd">
                        <x-filament::badge>
                            {{ count($attributions) }}
                        </x-filament::badge>
                    </x-slot>
                    <div class="flex flex-col items-stretch divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($attributions as $attribution)
                            <div class="py-2 text-center">
                                <span class="font-bold">
                                    {{ $attribution['start_date'] }} &rarr; {{ $attribution['end_date'] }}
                                </span>
                                <br />
                                <span class="text-sm italic">
                                    {{ implode(', ', $attribution['allowed_halfdays']) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </x-filament::section>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
