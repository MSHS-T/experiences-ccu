<x-filament-panels::page>
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-4 lg:h-[80vh]">
        <nav class="flex flex-1 flex-col" aria-label="Sidebar">
            <ul role="list" class="-mx-2 space-y-1 flex flex-1 flex-col items-stretch">
                @foreach ($this->manipulations as $id => $name)
                    @if (isset($this->manipulationId) && $id === $this->manipulationId)
                        <button type="button" wire:click="setManipulation({{ $id }})"
                            wire:loading.attr="disabled"
                            class="bg-gray-200 dark:bg-gray-800 text-indigo-600 dark:text-indigo-500 group flex gap-x-3 rounded-md p-2 pl-3 text-sm leading-6 font-semibold">
                            {{ $name }}
                        </button>
                    @else
                        <li>
                            <button type="button" wire:click="setManipulation({{ $id }})"
                                wire:loading.attr="disabled"
                                class="text-gray-700 hover:text-indigo-600 hover:bg-gray-200 group flex gap-x-3 rounded-md p-2 pl-3 text-sm leading-6 font-semibold">
                                {{ $name }}
                            </button>
                        </li>
                    @endif
                @endforeach
            </ul>
        </nav>
        <div class="lg:col-span-4 flex flex-col items-stretch overflow-y-scroll">
            {{ $this->table }}
        </div>
    </div>
</x-filament-panels::page>
