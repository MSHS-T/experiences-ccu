@extends('layouts.public')

@section('title', __('public.home.page_title'))

@section('content')
    <div
        class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 text-center lg:pt-8 min-h-[95vh] flex flex-col justify-between items-center">
        <div class="h-12 lg:h-16"></div>
        <div class="">
            <h1
                class="mx-auto max-w-5xl font-display text-3xl font-medium tracking-tight text-slate-900 sm:text-5xl md:text-6xl">
                {{ __('public.home.title1') }}
                <span class="relative text-blue-600 whitespace-nowrap">
                    <svg aria-hidden="true" viewBox="0 0 418 42"
                        class="absolute left-0 top-2/3 h-[0.58em] w-full fill-blue-300/70" preserveAspectRatio="none">
                        <path
                            d="M203.371.916c-26.013-2.078-76.686 1.963-124.73 9.946L67.3 12.749C35.421 18.062 18.2 21.766 6.004 25.934 1.244 27.561.828 27.778.874 28.61c.07 1.214.828 1.121 9.595-1.176 9.072-2.377 17.15-3.92 39.246-7.496C123.565 7.986 157.869 4.492 195.942 5.046c7.461.108 19.25 1.696 19.17 2.582-.107 1.183-7.874 4.31-25.75 10.366-21.992 7.45-35.43 12.534-36.701 13.884-2.173 2.308-.202 4.407 4.442 4.734 2.654.187 3.263.157 15.593-.78 35.401-2.686 57.944-3.488 88.365-3.143 46.327.526 75.721 2.23 130.788 7.584 19.787 1.924 20.814 1.98 24.557 1.332l.066-.011c1.201-.203 1.53-1.825.399-2.335-2.911-1.31-4.893-1.604-22.048-3.261-57.509-5.556-87.871-7.36-132.059-7.842-23.239-.254-33.617-.116-50.627.674-11.629.54-42.371 2.494-46.696 2.967-2.359.259 8.133-3.625 26.504-9.81 23.239-7.825 27.934-10.149 28.304-14.005.417-4.348-3.529-6-16.878-7.066Z">
                        </path>
                    </svg>
                    <span class="relative">
                        {{ __('public.home.title2') }}
                    </span>
                </span>
            </h1>
            <div class="mx-auto pt-6 max-w-3xl taxt-base lg:text-lg tracking-tight text-slate-700 text-justify">
                {!! $presentation_text !!}
            </div>
            <div class="mt-10 flex justify-center gap-x-6">
                <a class="group inline-flex ring-1 items-center justify-center rounded-full py-2 px-4 text-sm focus:outline-none ring-slate-200 text-slate-700 hover:text-slate-900 hover:ring-slate-300 active:bg-slate-100 active:text-slate-600 focus-visible:outline-blue-600 focus-visible:ring-slate-300"
                    href="#manipulations">
                    <x-fas-arrow-down class="h-4 w-4 fill-blue-600" />
                    <span class="ml-3">{{ __('public.home.show_manips') }}</span>
                </a>
            </div>
        </div>
        <div class="pt-8 mb-8 flex justify-center">
            <img alt="Maison des Sciences Humaines et de la Société de Toulouse" class="h-12 lg:h-16"
                src="{{ Vite::asset('resources/images/mshs-toulouse.png') }}">
        </div>
    </div>
    <section id="manipulations" class="relative overflow-hidden bg-blue-600 pb-28 pt-20 sm:py-32 min-h-screen">
        <img alt="" loading="lazy" width="2245" height="1636" decoding="async" data-nimg="1"
            class="absolute left-1/2 top-1/2 max-w-none translate-x-[-44%] translate-y-[-42%]" style="color:transparent"
            src="{{ Vite::asset('resources/images/background-features.jpg') }}">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 relative">
            <div class="max-w-2xl md:mx-auto md:text-center xl:max-w-none">
                <h2 class="font-display text-3xl tracking-tight text-white sm:text-4xl md:text-5xl">
                    {{ __('public.home.manip_list') }}
                </h2>
            </div>
            @if ($manipulations->isEmpty())
                <p class="mt-32 text-xl text-center tracking-tight text-blue-100">
                    {{ __('public.home.no_manips') }}
                </p>
            @else
                <div class="mt-8 grid grid-cols-1 items-center gap-y-2 pt-10 sm:gap-y-6 md:mt-16 lg:mt-24 lg:grid-cols-9 lg:pt-0"
                    x-data="{ selected: {{ $selectedManipulation }} }">
                    <div class="-mx-4 flex overflow-x-auto pb-4 sm:mx-0 lg:overflow-visible lg:pb-0 lg:col-span-3">
                        <div class="relative w-full z-10 flex space-x-4 whitespace-nowrap px-4 sm:mx-auto sm:px-0 lg:mx-0 lg:block lg:space-x-0 lg:space-y-2 lg:whitespace-normal lg:py-12"
                            role="tablist" aria-orientation="vertical">
                            @foreach ($manipulations as $m)
                                <div class="group relative rounded-full px-4 py-1 lg:rounded-l-xl lg:rounded-r-none lg:p-6"
                                    x-bind:class="selected === {{ $m->id }} ?
                                        'bg-white lg:bg-white/10 lg:ring-1 lg:ring-inset lg:ring-white/10' :
                                        'hover:bg-white/10 lg:hover:bg-white/5'">
                                    <h3>
                                        <button
                                            class="font-display text-lg [&:not(:focus-visible)]:focus:outline-none whitespace-normal text-start"
                                            x-bind:class="selected === {{ $m->id }} ?
                                                'text-blue-600 lg:text-white' :
                                                'text-blue-100 hover:text-white lg:text-white'"
                                            x-on:click="selected = {{ $m->id }}" role="tab" type="button">
                                            <span
                                                class="absolute inset-0 rounded-full lg:rounded-l-xl lg:rounded-r-none"></span>
                                            {{ $m->name }}
                                        </button>
                                    </h3>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="lg:col-span-6 min-h-[50vh]">
                        @foreach ($manipulations as $i => $m)
                            <div role="tabpanel" tabindex="{{ $i }}"
                                x-bind:class="selected === {{ $m->id }} ? 'block' : 'hidden'"
                                class="h-full w-full flex flex-col items-stretch justify-center p-4 sm:p-10 rounded-xl bg-white shadow-xl shadow-blue-900/20 sm:w-auto lg:mt-0 space-y-4">
                                {{-- <div class=" grid grid-cols-1 lg:grid-cols-6 items-start gap-x-8 gap-y-4"> --}}
                                <div class="flex flex-col lg:flex-row items-stretch lg:space-x-8">
                                    <div class="lg:col-span-6">
                                        <h3
                                            class="font-display text-2xl font-bold tracking-tight text-slate-900 flex items-center px-3 space-x-2">
                                            <x-fas-circle-info class="h-5 w-5" />
                                            <span>
                                                @lang('attributes.description')
                                            </span>
                                        </h3>
                                        <div
                                            class="rounded-2xl bg-slate-100 px-4 py-2 sm:px-4 sm:py-3 text-base font-medium text-slate-700 tracking-tight ">
                                            {!! $m->description !!}
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-col lg:flex-row items-stretch lg:space-x-8">
                                    <div class="lg:basis-1/2">
                                        <h3
                                            class="font-display text-2xl font-bold tracking-tight text-slate-900 flex items-center px-3 space-x-2">
                                            <x-fas-stopwatch class="h-5 w-5" />
                                            <span>
                                                @lang('attributes.duration')
                                            </span>
                                        </h3>
                                        <div
                                            class="rounded-2xl bg-slate-100 px-4 py-2 sm:px-4 sm:py-3 text-base font-medium text-slate-700 tracking-tight ">
                                            {{ $m->duration }} minutes
                                        </div>
                                    </div>
                                    <div class="lg:basis-1/2">
                                        <h3
                                            class="font-display text-2xl font-bold tracking-tight text-slate-900 flex items-center px-3 space-x-2">
                                            <x-fas-calendar-days class="h-5 w-5" />
                                            <span>
                                                @lang('attributes.dates')
                                            </span>
                                        </h3>
                                        <div
                                            class="rounded-2xl bg-slate-100 px-4 py-2 sm:px-4 sm:py-3 text-base font-medium text-slate-700 tracking-tight flex items-center">
                                            {{ $m->start_date->format('d/m/Y') }}
                                            <x-fas-arrow-right class="h-3 w-3 mx-1" />
                                            {{ $m->end_date->format('d/m/Y') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="flex flex-col lg:flex-row items-stretch lg:space-x-8">
                                    <div class="lg:basis-2/3 h-full flex flex-col">
                                        <h3
                                            class="font-display text-2xl font-bold tracking-tight text-slate-900 flex items-center px-3 space-x-2">
                                            <x-fas-list-check class="h-5 w-5" />
                                            <span>
                                                @lang('attributes.requirements')
                                            </span>
                                        </h3>
                                        <ol role="list"
                                            class="flex-grow divide-y divide-slate-300/30 rounded-2xl bg-slate-100 px-4 py-2 sm:px-4 sm:py-3 text-base font-medium text-slate-700 tracking-tight ">
                                            @foreach ($m->requirements as $r)
                                                <li class="flex justify-between py-1 sm:py-3"
                                                    aria-label="Strokes and fills on page 21">
                                                    <span class="font-medium text-slate-900"
                                                        aria-hidden="true">{{ $r }}</span>
                                                </li>
                                            @endforeach
                                        </ol>
                                    </div>
                                    <div class="lg:basis-1/3 h-full flex flex-col">
                                        <h3
                                            class="font-display text-2xl font-bold tracking-tight text-slate-900 flex items-center px-3 space-x-2">
                                            <x-fas-location-dot class="h-5 w-5" />
                                            <span>
                                                @lang('attributes.location')
                                            </span>
                                        </h3>
                                        <div
                                            class="flex-grow rounded-2xl bg-slate-100 px-4 py-2 sm:px-4 sm:py-3 text-base font-medium text-slate-700 tracking-tight space-y-4 access_instructions">
                                            {!! $access_instructions !!}
                                            <div class="flex justify-center">
                                                <a href="{{ asset(config('collabccu.access_map')) }}" target="_blank"
                                                    class="inline-flex items-center gap-x-1.5 rounded-full px-4 py-2 text-sm font-medium text-blue-700 ring-1 ring-inset ring-blue-200">
                                                    <x-fas-route class="h-3 w-3" />
                                                    {{ __('public.home.access_instructions') }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="lg:col-span-6 flex justify-end items-end">
                                    <a class="group inline-flex items-center justify-center space-x-2 rounded-full py-2 px-4 text-sm font-semibold focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 bg-blue-600 text-white hover:text-slate-100 hover:bg-blue-500 active:bg-blue-800 active:text-blue-100 focus-visible:outline-blue-600"
                                        href="{{ route('manipulation_slots', ['manipulation' => $m]) }}">
                                        <span>
                                            {{ __('public.home.show_slots') }}
                                        </span>
                                        <x-fas-arrow-right class="h-5 w-5" />
                                    </a>
                                </div>
                                {{-- </div> --}}
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>

@endsection
