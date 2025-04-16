<x-filament-panels::page>
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-4 lg:h-[80vh]">
        <nav class="flex flex-1 flex-col" aria-label="Sidebar">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white pb-2">
                {{ __('admin.choose_manipulation') }}:
            </h3>
            <ul role="list" class="-mx-2 pl-4 space-y-1 flex flex-1 flex-col items-stretch">
                @foreach ($this->manipulations as $id => $name)
                    @if (isset($this->manipulationId) && $id === $this->manipulationId)
                        <button type="button" wire:loading.attr="disabled"
                            class="bg-gray-200 dark:bg-gray-800 text-indigo-600 dark:text-indigo-500 group flex items-center gap-x-3 rounded-md p-2 pl-3 text-sm leading-6 font-semibold">
                            <span class="flex-1 text-left">{{ $name }}</span>
                            <x-fas-chevron-right class="w-4 h-4" />
                        </button>
                    @else
                        <button type="button" wire:click="setManipulation({{ $id }})"
                            wire:loading.attr="disabled"
                            class="text-gray-700 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-500 hover:bg-gray-200 dark:hover:bg-gray-800 group flex items-center gap-x-3 rounded-md p-2 pl-3 text-sm leading-6 font-semibold group">
                            <span class="flex-1 text-left">{{ $name }}</span>
                            <x-fas-chevron-right class="w-4 h-4 invisible group-hover:visible" />
                        </button>
                    @endif
                @endforeach
            </ul>
        </nav>
        <div class="lg:col-span-4 flex flex-col items-stretch overflow-y-scroll">
            {{ $this->table }}
        </div>
    </div>
</x-filament-panels::page>
