<x-filament-panels::page>
    <form wire:submit="searchSubject">
        <div
            class="flex flex-col sm:flex-row items-stretch sm:items-end justify-end sm:justify-start gap-x-6 w-full xl:w-auto">
            {{ $this->form }}
        </div>
    </form>
    <hr />
    <div class="flex-1 max-w-full flex justify-center">
        @if ($notFound)
            <div class="flex justify-center items-center space-x-6 w-full">
                <x-fas-ban class="h-8 w-8" />
                <span>
                    Aucun résultat trouvé
                </span>
            </div>
        @elseif(filled($subjectHistory))
            <div class="flex-shrink flex-grow max-w-full p-4" style="height: 40rem;">
                <dl class="mx-auto grid grid-cols-1 gap-px bg-white dark:bg-gray-900/5 sm:grid-cols-2 xl:grid-cols-4">
                    <div
                        class="flex flex-col items-stretch justify-between gap-x-4 gap-y-2 bg-white dark:bg-gray-900 px-4 py-8 sm:py-12 sm:px-6 xl:px-8 border border-gray-200 rounded-t-xl sm:rounded-tr-none sm:rounded-tl-xl xl:rounded-l-xl">
                        <dt class="text-sm font-medium leading-6 text-gray-500 dark:text-gray-400">
                            Inscriptions
                        </dt>
                        <dd
                            class="w-full flex justify-between items-baseline pb-2 sm:pb-7 text-3xl leading-10 tracking-tight text-gray-900 dark:text-white">
                            <p class="font-semibold">
                                {{ $this->subjectHistory['made'] }}
                            </p>
                        </dd>
                    </div>
                    <div
                        class="flex flex-col items-stretch justify-between gap-x-4 gap-y-2 bg-white dark:bg-gray-900 px-4 py-8 sm:py-12 sm:px-6 xl:px-8 border border-gray-200 sm:rounded-tr-xl xl:rounded-tr-none">
                        <dt class="text-sm font-medium leading-6 text-gray-500 dark:text-gray-400">
                            Inscriptions confirmées
                        </dt>
                        <dd
                            class="w-full flex justify-between items-baseline pb-2 sm:pb-7 text-3xl leading-10 tracking-tight text-gray-900 dark:text-white">
                            <p class="font-semibold">
                                {{ $this->subjectHistory['confirmed'] }}
                            </p>
                            <p @class([
                                'ml-2 mr-4 flex items-baseline text-sm font-semibold' => true,
                                'text-green-600' => $this->subjectHistory['confirmed_percentage'] >= 75,
                                'text-orange-600' =>
                                    $this->subjectHistory['confirmed_percentage'] >= 50 &&
                                    $this->subjectHistory['confirmed_percentage'] < 75,
                                'text-red-600' => $this->subjectHistory['confirmed_percentage'] < 50,
                            ])>
                                {{ round($this->subjectHistory['confirmed_percentage']) }}%
                            </p>
                        </dd>
                    </div>
                    <div
                        class="flex flex-col items-stretch justify-between gap-x-4 gap-y-2 bg-white dark:bg-gray-900 px-4 py-8 sm:py-12 sm:px-6 xl:px-8 border border-gray-200 sm:rounded-bl-xl xl:rounded-bl-none">
                        <dt class="text-sm font-medium leading-6 text-gray-500 dark:text-gray-400">
                            Inscriptions confirmées honorées
                        </dt>
                        <dd
                            class="w-full flex justify-between items-baseline pb-2 sm:pb-7 text-3xl leading-10 tracking-tight text-gray-900 dark:text-white">
                            <p class="font-semibold">
                                {{ $this->subjectHistory['confirmed_honored'] }}
                            </p>
                            <p @class([
                                'ml-2 mr-4 flex items-baseline text-sm font-semibold' => true,
                                'text-green-600' =>
                                    $this->subjectHistory['confirmed_honored_percentage'] >= 75,
                                'text-orange-600' =>
                                    $this->subjectHistory['confirmed_honored_percentage'] >= 50 &&
                                    $this->subjectHistory['confirmed_honored_percentage'] < 75,
                                'text-red-600' =>
                                    $this->subjectHistory['confirmed_honored_percentage'] < 50,
                            ])>
                                {{ round($this->subjectHistory['confirmed_honored_percentage']) }}%
                            </p>
                        </dd>
                    </div>
                    <div
                        class="flex flex-col items-stretch justify-between gap-x-4 gap-y-2 bg-white dark:bg-gray-900 px-4 py-8 sm:py-12 sm:px-6 xl:px-8 border border-gray-200 rounded-b-xl sm:rounded-bl-none sm:rounded-br-xl xl:rounded-r-xl">
                        <dt class="text-sm font-medium leading-6 text-gray-500 dark:text-gray-400">
                            Inscriptions non confirmées honorées
                        </dt>
                        <dd
                            class="w-full flex justify-between items-baseline pb-2 sm:pb-7 text-3xl leading-10 tracking-tight text-gray-900 dark:text-white">
                            <p class="font-semibold">
                                {{ $this->subjectHistory['unconfirmed_honored'] }}
                            </p>
                            <p @class([
                                'ml-2 mr-4 flex items-baseline text-sm font-semibold' => true,
                                'text-green-600' =>
                                    $this->subjectHistory['unconfirmed_honored_percentage'] >= 75,
                                'text-orange-600' =>
                                    $this->subjectHistory['unconfirmed_honored_percentage'] >= 50 &&
                                    $this->subjectHistory['unconfirmed_honored_percentage'] < 75,
                                'text-red-600' =>
                                    $this->subjectHistory['unconfirmed_honored_percentage'] < 50,
                            ])>
                                {{ round($this->subjectHistory['unconfirmed_honored_percentage']) }}%
                            </p>
                        </dd>
                    </div>
                </dl>
                <div class="bg-white dark:bg-gray-900 border border-gray-200 shadow sm:rounded-xl mt-4">
                    <div class="px-4 py-5 sm:p-6 sm:flex sm:items-start sm:justify-between">
                        <div class="max-w-xl text-sm text-gray-500 dark:text-gray-400">
                            <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-white">
                                {{ $subjectHistory['blocked'] ? 'Participant bloqué' : 'Participant actif' }}
                            </h3>
                            <p>
                                Un compte bloqué ne peut pas s'inscrire aux manipulations.
                            </p>
                        </div>
                        <div class="mt-5 sm:ml-6 sm:mt-0 sm:flex sm:flex-shrink-0 sm:items-center">
                            <button type="button" wire:click="toggleBlock"
                                class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500">
                                {{ $subjectHistory['blocked'] ? 'Débloquer' : 'Bloquer' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
