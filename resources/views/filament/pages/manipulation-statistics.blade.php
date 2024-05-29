<x-filament-panels::page>
    <form wire:submit="computeStatistics">
        <div
            class="flex flex-col sm:flex-row items-stretch sm:items-end justify-end sm:justify-start gap-x-6 w-full xl:w-auto">
            {{ $this->form }}
        </div>
    </form>
    <hr />
    <div class="flex-1 max-w-full flex justify-center">
        @if ($this->statistics !== null)
            <table class="mt-6 w-full whitespace-nowrap text-left">
                <colgroup>
                    <col class="w-full sm:w-7/12">
                    <col class="lg:w-1/12">
                    <col class="lg:w-1/12">
                    <col class="lg:w-1/12">
                    <col class="lg:w-1/12">
                    <col class="lg:w-1/12">
                </colgroup>
                <thead class="border-b border-gray-900/10 dark:border-white/10 text-sm leading-6 dark:text-white">
                    <tr>
                        <th scope="col" class="py-2 pl-4 pr-8 font-semibold sm:pl-6 lg:pl-8">Plateau</th>
                        <th scope="col" class="py-2 pl-0 pr-8 font-semibold">Nombre de créneaux</th>
                        <th scope="col" class="py-2 pl-0 pr-8 font-semibold">Nombre d'heures</th>
                        <th scope="col" class="py-2 pl-0 pr-8 font-semibold">Taux d'inscription</th>
                        <th scope="col" class="py-2 pl-0 pr-8 font-semibold">Taux de confirmation</th>
                        <th scope="col" class="py-2 pl-0 pr-8 font-semibold">Taux de présence</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-900/5 dark:divide-white/5">
                    @foreach ($this->statistics as $row)
                        <tr>
                            <td class="py-4 pl-4 pr-8 sm:pl-6 lg:pl-8">
                                <div class="flex items-center gap-x-4">
                                    <div class="h-8 w-8 rounded-full"
                                        style="background-color: {{ $row->plateau->color }}"></div>
                                    <div class="truncate text-sm font-medium leading-6 dark:text-white">
                                        {{ $row->plateau->name }}</div>
                                </div>
                            </td>
                            <td class="py-4 pl-0 pr-4 text-sm leading-6 sm:pr-8 lg:pr-20">{{ $row->slot_count }}</td>
                            <td class="py-4 pl-0 pr-4 text-sm leading-6 sm:pr-8 lg:pr-20">{{ $row->hour_count }} h</td>
                            <td class="py-4 pl-0 pr-4 text-sm leading-6 sm:pr-8 lg:pr-20">
                                {{ round($row->booking_rate, 2) }} %
                            </td>
                            <td class="py-4 pl-0 pr-4 text-sm leading-6 sm:pr-8 lg:pr-20">
                                {{ round($row->confirmation_rate, 2) }}
                                %</td>
                            <td class="py-4 pl-0 pr-4 text-sm leading-6 sm:pr-8 lg:pr-20">
                                {{ round($row->presence_rate, 2) }} %
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-t border-gray-900/10 dark:border-white/10">
                        <th scope="row" class="pl-4 pr-3 pt-6 sm:pl-6 lg:pl-8 text-sm font-normal text-gray-500">
                            <div class="flex items-center gap-x-4">
                                <div
                                    class="h-8 w-8 rounded-full bg-gray-700/5 dark:bg-gray-200/5 flex items-center justify-center">
                                    <x-fas-equals class="h-4 w-4" />
                                </div>
                                <div class="truncate text-sm font-medium leading-6">
                                    Total
                                </div>
                            </div>
                        </th>
                        <td class="pl-0 pr-4 pt-6 text-sm text-gray-500">
                            {{ $this->statistics->pluck('slot_count')->sum() }}</td>
                        <td class="pl-0 pr-4 pt-6 text-sm text-gray-500">
                            {{ $this->statistics->pluck('hour_count')->sum() }} h</td>
                        <td class="pl-0 pr-4 pt-6 text-sm text-gray-500">
                            {{ round($this->statistics->reduce(fn($total, $item) => $total + $item->booking_rate * $item->slot_count) / $this->statistics->pluck('slot_count')->sum(), 2) }}
                            %
                        </td>
                        <td class="pl-0 pr-4 pt-6 text-sm text-gray-500">
                            {{ round($this->statistics->reduce(fn($total, $item) => $total + $item->confirmation_rate * $item->slot_count) / $this->statistics->pluck('slot_count')->sum(), 2) }}
                            %
                        </td>
                        <td class="pl-0 pr-4 pt-6 text-sm text-gray-500">
                            {{ round($this->statistics->reduce(fn($total, $item) => $total + $item->presence_rate * $item->slot_count) / $this->statistics->pluck('slot_count')->sum(), 2) }}
                            %
                        </td>
                    </tr>
                </tfoot>
            </table>
        @endif
    </div>
</x-filament-panels::page>
