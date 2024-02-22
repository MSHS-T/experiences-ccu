@extends('layouts.public')

@section('title', __('public.slots.page_title', ['manipulation' => $manipulationName]))

@section('content')
    <div x-data="slotSelector">
        <section id="slots" class="relative overflow-hidden bg-blue-600 pb-28 pt-20 sm:py-32 lg:min-h-screen">
            <img alt="" loading="lazy" width="2245" height="1636" decoding="async" data-nimg="1"
                class="absolute left-1/2 top-1/2 max-w-none translate-x-[-44%] translate-y-[-42%]" style="color:transparent"
                src="{{ Vite::asset('resources/images/background-features.jpg') }}">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 relative">
                <div class="max-w-2xl md:mx-auto md:text-center xl:max-w-none">
                    <h2 class="font-display text-3xl tracking-tight text-white sm:text-4xl md:text-5xl">
                        {{ __('public.slots.page_title', ['manipulation' => $manipulationName]) }}
                    </h2>
                    <h3 class="font-display text-2xl tracking-tight text-white sm:text-3xl md:text-4xl">
                        {{ $manipulationName }}
                    </h3>
                </div>
                @if ($slots->isEmpty())
                    <p class="mt-32 text-xl text-center tracking-tight text-blue-100">
                        {{ __('public.slots.no_slots') }}
                    </p>
                @else
                    <div class="mt-8 grid grid-cols-1 items-center gap-y-2 pt-10 sm:gap-y-6 md:mt-16 lg:pt-0">
                        <a href="#details" class="hidden" x-ref="nextSectionLink"></a>
                        {{-- {{ dump($slots) }} --}}
                        <template x-for="dayIndex in daysDisplayed">
                            <template x-if="dayIndex <= Object.keys(slots).length">
                                <div class="rounded-xl divide-y divide-white/10"
                                    x-bind:class="selectedDate === Object.keys(slots)[dayIndex - 1] ?
                                        'bg-white/10 ring-1 ring-inset ring-white/10' :
                                        'hover:bg-white/5'">
                                    <div class="group relative px-4 py-1 lg:p-3">
                                        <h2>
                                            <button
                                                class="font-display text-lg [&:not(:focus-visible)]:focus:outline-none
                                        flex items-center justify-between w-full p-1 font-medium text-left text-white"
                                                x-on:click="selectedDate = Object.keys(slots)[dayIndex - 1]" type="button">
                                                <span class="absolute inset-0 rounded-xl"></span>
                                                <span class="capitalize"
                                                    x-text="dayjs(Object.keys(slots)[dayIndex - 1]).format('dddd DD MMMM YYYY')"></span>

                                                <x-fas-caret-down class="h-5 w-5 transition"
                                                    x-bind:class="selectedDate === Object.keys(slots)[dayIndex - 1] && 'rotate-180'" />
                                            </button>
                                            </h3>
                                    </div>
                                    <div
                                        x-bind:class="selectedDate === Object.keys(slots)[dayIndex - 1] ? 'block' : 'hidden'">
                                        <div class="p-5 flex items-center flex-wrap space-x-2">
                                            <template x-for="slot in Object.values(slots)[dayIndex-1]">
                                                <button type="button"
                                                    class="inline-flex items-center justify-center rounded-full py-2 px-4 text-sm font-semibold"
                                                    x-key="slot.id" x-on:click="selectSlot(slot.id)"
                                                    x-bind:class="selectedSlot === slot.id ?
                                                        'bg-green-600 text-white hover:text-slate-100 hover:bg-green-500 border-2 border-green-500' :
                                                        'bg-white text-blue-600 hover:text-blue-500 hover:bg-slate-100'"
                                                    href="/mes-inscriptions" x-text="slot.start">
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </template>
                        <div class="flex justify-center" x-show="daysDisplayed < Object.keys(slots).length">
                            <button
                                class="group inline-flex ring-1 items-center justify-center rounded-full py-2 px-4 text-sm focus:outline-none ring-slate-200 text-white hover:text-slate-900 hover:ring-slate-300 active:bg-slate-100 active:text-slate-600 focus-visible:outline-blue-600 focus-visible:ring-slate-300"
                                type="button" x-on:click="loadMoreDays">
                                <x-fas-arrow-down class="h-4 w-4" />
                                <span class="ml-3">{{ __('public.slots.show_more_dates') }}</span>
                            </button>
                        </div>
                @endif
            </div>
        </section>
        <section id="details" class="relative overflow-hidden bg-white pb-28 pt-20 sm:py-32 min-h-screen">
            <h2 class="font-display text-3xl tracking-tight text-slate-900 text-center sm:text-4xl md:text-5xl">
                {{ __('public.slots.details') }}
            </h2>
            <form class="mt-8 md:mt-20 px-4 mx-auto max-w-5xl grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6"
                method="POST" x-bind:action="'{{ route('book_slot', ['slot' => 'XX']) }}'.replace('XX', selectedSlot)">
                @csrf
                <input type="hidden" name="slot_id" x-bind:value="selectedSlot">
                <div class="sm:col-span-3">
                    <label for="first-name" class="block text-sm font-medium leading-6 text-gray-900">
                        {{ __('attributes.first_name') }}
                    </label>
                    <div class="mt-2">
                        <input type="text" name="first-name" id="first-name" autocomplete="given-name"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label for="last-name" class="block text-sm font-medium leading-6 text-gray-900">
                        {{ __('attributes.last_name') }}
                    </label>
                    <div class="mt-2">
                        <input type="text" name="last-name" id="last-name" autocomplete="family-name"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                    </div>
                </div>

                <div class="sm:col-span-6">
                    <label for="email" class="block text-sm font-medium leading-6 text-gray-900">
                        {{ __('attributes.email') }}
                    </label>
                    <div class="mt-2">
                        <input id="email" name="email" type="email" autocomplete="email"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                    </div>
                </div>

                <fieldset class="sm:col-span-6">
                    <legend class="text-sm font-semibold leading-6 text-gray-900">
                        {{ __('public.slots.check_requirements') }}
                    </legend>
                    <div class="mt-6 space-y-6">
                        @foreach ($manipulationRequirements as $i => $r)
                            <div class="relative flex gap-x-3">
                                <div class="flex h-6 items-center">
                                    <input id="requirements-{{ $i }}" name="requirements" type="checkbox"
                                        value="{{ $i }}"
                                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                </div>
                                <div class="text-sm leading-6">
                                    <label for="requirements-{{ $i }}"
                                        class="font-medium text-gray-500">{{ $r }}</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </fieldset>

                <div class="sm:col-span-6 pt-4 flex items-start">
                    <input id="commitment" name="commitment" type="checkbox"
                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600 mr-3">
                    <label for="commitment" class="block text-sm font-medium leading-6 text-gray-900">
                        {{ __('public.slots.commitment') }}
                    </label>
                </div>

                <div class="sm:col-span-6 flex justify-center">
                    <button type="submit"
                        class="group inline-flex items-center justify-center rounded-full py-2 px-4 text-sm font-semibold focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 bg-blue-600 text-white hover:text-slate-100 hover:bg-blue-500 active:bg-blue-800 active:text-blue-100 focus-visible:outline-blue-600">
                        {{ __('public.slots.confirm_booking') }}
                    </button>
                </div>
            </form>
        </section>
    </div>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('slotSelector', () => ({
                slots: @json($slots),
                selectedDate: '{{ $slots->keys()->first() }}',
                selectedSlot: null,
                daysDisplayed: 7,

                loadMoreDays() {
                    this.daysDisplayed += 5;
                },

                selectSlot(slotId) {
                    this.selectedSlot = slotId;
                    this.$refs.nextSectionLink.click();
                },
            }))
        })
    </script>
@endsection
