<x-public-layout>
    <x-slot name="title">{{ __('public.my_bookings.page_title') }}</x-slot>
    <div class="h-full flex flex-col justify-start items-stretch px-6 py-12 lg:px-8 space-y-10">
        @if (session('success'))
            <div class="border-l-4 border-green-400 bg-green-50 p-4 sm:mx-auto sm:w-full sm:max-w-lg">
                <div class="flex">
                    <div class="shrink-0">
                        <x-fas-check-circle class="h-5 w-5 text-green-400" />
                    </div>
                    <div class="ml-3">
                        <div class="text-sm text-green-700">
                            <p>
                                {{ session('success') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <div class="sm:mx-auto sm:w-full sm:max-w-sm">
            <h2 class="mt-10
        text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">
                {{ __('public.my_bookings.page_title') }}
            </h2>
        </div>
        <div class="grow flex flex-col items-stretch justify-between space-y-4">
            @if ($history['blocked'])
                <div
                    class="self-center relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left border border-gray-300 sm:my-8 sm:w-full sm:max-w-sm sm:p-6">
                    <div>
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
                            <x-fas-circle-exclamation class="h-6 w-6 text-red-600" />
                        </div>
                        <div class="mt-3 text-center sm:mt-5">
                            <h3 class="text-base font-semibold leading-6 text-gray-900" id="modal-title">
                                {{ __('public.my_bookings.blocked') }}
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    {{ __('public.my_bookings.blocked_description') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <ul role="list" class="divide-y divide-gray-100 w-full lg:max-w-[30vw] lg:mx-auto">
                    @foreach ($bookings as $booking)
                        <li
                            class="flex items-center justify-between gap-x-6 py-3 px-4 rounded-lg border border-gray-300">
                            <div class="min-w-0">
                                <div class="flex items-start gap-x-3">
                                    <p class="text-sm font-semibold leading-6 text-gray-900">
                                        {{ $booking->slot->manipulation->name }}
                                    </p>
                                    @if ($booking->confirmed)
                                        <p
                                            class="rounded-md whitespace-nowrap mt-0.5 px-1.5 py-0.5 text-xs font-medium ring-1 ring-inset text-green-700 bg-green-50 ring-green-600/20 flex items-center space-x-1">
                                            <x-fas-check class="h-3 w-3" />
                                            <span>
                                                {{ __('public.my_bookings.confirmed') }}
                                            </span>
                                        </p>
                                    @else
                                        <p
                                            class="rounded-md whitespace-nowrap mt-0.5 px-1.5 py-0.5 text-xs font-medium ring-1 ring-inset text-red-700 bg-red-50 ring-red-600/20 flex items-center space-x-1">
                                            <x-fas-triangle-exclamation class="h-3 w-3" />
                                            <span>
                                                {{ __('public.my_bookings.unconfirmed') }}
                                            </span>
                                        </p>
                                    @endif
                                </div>
                                <div class="mt-1 flex items-center gap-x-2 text-xs leading-5 text-gray-500">
                                    <p class="whitespace-nowrap">
                                        <time
                                            datetime="{{ $booking->slot->start->toString() }}">{{ \Illuminate\Support\Str::headline($booking->slot->start->translatedFormat('l d F Y')) }}</time>
                                    </p>
                                    <svg viewBox="0 0 2 2" class="h-0.5 w-0.5 fill-current">
                                        <circle cx="1" cy="1" r="1" />
                                    </svg>
                                    <p class="whitespace-nowrap">
                                        {{ $booking->slot->start->translatedFormat('H\hi') . '-' . $booking->slot->end->translatedFormat('H\hi') }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex flex-none items-center gap-x-4">
                                @unless ($booking->confirmed)
                                    <a href="{{ route('confirm_booking', $booking) }}"
                                        class="hidden rounded-md bg-white px-2.5 py-1.5 text-sm font-semibold text-blue-900 shadow-xs ring-1 ring-inset ring-blue-300 hover:bg-blue-50 sm:block">
                                        {{ __('public.my_bookings.confirm') }}
                                    </a>
                                @endunless
                                <a href="{{ route('cancel_booking', $booking) }}"
                                    class="hidden rounded-md bg-white px-2.5 py-1.5 text-sm font-semibold text-gray-900 shadow-xs ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:block">
                                    {{ __('public.my_bookings.cancel') }}
                                </a>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
            <dl
                class="lg:mx-auto lg:max-w-[50vw] grid grid-rows-4 lg:grid-rows-1 lg:grid-cols-4 divide-y lg:divide-x lg:divide-y-0 divide-white bg-gray-100 sm:grid-cols-2 border border-gray-100 rounded-xl">
                <div
                    class="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-2 px-4 py-4 lg:py-8 sm:px-6 xl:px-8">
                    <dt class="text-sm font-medium leading-6 text-gray-500">{{ __('public.my_bookings.history.made') }}
                    </dt>
                    <dd class="w-full flex-none text-3xl font-medium leading-10 tracking-tight text-gray-900">
                        {{ $history['made'] }}
                    </dd>
                </div>
                <div
                    class="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-2 px-4 py-4 lg:py-8 sm:px-6 xl:px-8">
                    <dt class="text-sm font-medium leading-6 text-gray-500">
                        {{ __('public.my_bookings.history.confirmed') }}</dt>
                    <dd class="w-full flex-none text-3xl font-medium leading-10 tracking-tight text-gray-900">
                        {{ $history['confirmed'] }}
                    </dd>
                </div>
                <div
                    class="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-2 px-4 py-4 lg:py-8 sm:px-6 xl:px-8">
                    <dt class="text-sm font-medium leading-6 text-gray-500">
                        {{ __('public.my_bookings.history.confirmed_honored') }}</dt>
                    <dd class="w-full flex-none text-3xl font-medium leading-10 tracking-tight text-gray-900">
                        {{ $history['confirmed_honored'] }}
                    </dd>
                </div>
                <div
                    class="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-2 px-4 py-4 lg:py-8 sm:px-6 xl:px-8">
                    <dt class="text-sm font-medium leading-6 text-gray-500">
                        {{ __('public.my_bookings.history.unconfirmed_honored') }}</dt>
                    <dd class="w-full flex-none text-3xl font-medium leading-10 tracking-tight text-gray-900">
                        {{ $history['unconfirmed_honored'] }}
                    </dd>
                </div>
            </dl>

        </div>
    </div>
</x-public-layout>
