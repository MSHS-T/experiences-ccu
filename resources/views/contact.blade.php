<x-public-layout>
    <x-slot name="title">{{ __('public.contact.page_title') }}</x-slot>

    <div class="flex flex-col justify-center px-6 py-12 lg:px-8 space-y-10">
        <div class="sm:mx-auto sm:w-full sm:max-w-sm">
            {{-- <img class="mx-auto h-10 w-auto" src="https://tailwindui.com/img/logos/mark.svg?color=indigo&shade=600" alt="Your Company"> --}}
            <h2 class="mt-10
        text-center text-xl font-bold leading-9 tracking-tight text-gray-900">
                {{ __('public.contact.text') }}
            </h2>
        </div>

        @session('success')
            <div class="border-l-4 border-green-400 bg-green-50 p-4 sm:mx-auto sm:w-full sm:max-w-lg">
                <div class="flex">
                    <div class="shrink-0">
                        <x-fas-check-circle class="h-5 w-5 text-green-400" />
                    </div>
                    <div class="ml-3">
                        <div class="text-sm text-green-700">
                            <p>
                                {{ $value }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endsession

        <div class="sm:mx-auto sm:w-full sm:max-w-sm">
            <form class="space-y-6" method="POST" action="{{ route('contact_send') }}">
                @csrf
                <div>
                    <label for="name" class="block text-sm font-medium leading-6 text-gray-900">Nom</label>
                    <div class="mt-2">
                        <input id="name" name="name" type="text" autocomplete="name" required
                            aria-describedby="name-error" value="{{ old('name') }}"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-xs ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600" id="name-error">{{ $message }}
                        </p>
                    @enderror
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium leading-6 text-gray-900">
                        Adresse email
                    </label>
                    <div class="mt-2">
                        <input id="email" name="email" type="email" autocomplete="email" required
                            aria-describedby="email-error" value="{{ old('email') }}"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-xs ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                    @error('email')
                        <p class="mt-1 text-sm text-red-600" id="email-error">{{ $message }}
                        </p>
                    @enderror
                </div>
                <div>
                    <label for="message" class="block text-sm font-medium leading-6 text-gray-900">
                        Message
                    </label>
                    <div class="mt-2">
                        <textarea id="message" name="message" required aria-describedby="message-error"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-xs ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">{{ old('message') }}</textarea>
                    </div>
                    @error('message')
                        <p class="mt-1 text-sm text-red-600" id="message-error">{{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <button type="submit"
                        class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-xs hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                        Recevoir un lien de connexion
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-public-layout>
