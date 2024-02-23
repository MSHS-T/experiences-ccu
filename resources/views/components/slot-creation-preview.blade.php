@if (count($slots) == 0)
    <div class="flex justify-center items-center space-x-6 w-full">
        <x-fas-triangle-exclamation class="h-8 w-8" />
        <span>
            {{ __('admin.no_slot_creatable_for_interval') }}
        </span>
    </div>
@else
    <h2 class="font-semibold">
        {{ __('admin.slot_creation_confirmation_intro') }}
    </h2>
    <div class="rounded-md border border-gray-300 mx-auto">
        <ul role="list" class="divide-y divide-gray-300">
            @foreach ($slots as $slot)
                <li class="px-6 py-4">
                    <ol role="list" class="flex items-center space-x-4">
                        <li class="text-sm font-semibold">
                            {{ \Illuminate\Support\Carbon::parse($slot['start'])->format('d/m/Y') }}
                        </li>
                        <li>
                            <x-fas-minus class="h-4 w-4 flex-shrink-0" />
                        </li>
                        <li class="text-sm font-medium">
                            {{ \Illuminate\Support\Carbon::parse($slot['start'])->format('H:i') }}
                        </li>
                        <li>
                            <x-fas-angle-right class="h-5 w-5 flex-shrink-0" />
                        </li>
                        <li class="ml-4 text-sm font-medium">
                            {{ \Illuminate\Support\Carbon::parse($slot['end'])->format('H:i') }}
                        </li>
                    </ol>
                </li>
            @endforeach
        </ul>
    </div>
    <p class="text-base italic text-center">
        {{ __('admin.slot_creation_confirmation_outro') }}
    </p>
@endif
