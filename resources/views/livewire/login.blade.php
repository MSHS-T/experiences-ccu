    <x-slot name="title">{{ __('public.my_bookings.page_title') }}</x-slot>

    <div class="flex min-h-screen flex-col justify-center px-6 py-12 lg:px-8 space-y-10">
        <div class="sm:mx-auto sm:w-full sm:max-w-sm">
            {{-- <img class="mx-auto h-10 w-auto" src="https://tailwindui.com/img/logos/mark.svg?color=indigo&shade=600" alt="Your Company"> --}}
            <h2 class="mt-10
        text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">
                Connectez-vous pour gérer vos inscriptions
            </h2>
        </div>

        @if (filled($error))
            <div class="border-l-4 border-red-400 bg-red-50 p-4 sm:mx-auto sm:w-full sm:max-w-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <x-fas-circle-xmark class="h-5 w-5 text-red-400" />
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Erreur</h3>
                        <div class="text-sm text-red-700">
                            <p>
                                {{ $error }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        @if (filled($success))
            <div class="border-l-4 border-green-400 bg-green-50 p-4 sm:mx-auto sm:w-full sm:max-w-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <x-fas-check-circle class="h-5 w-5 text-green-400" />
                    </div>
                    <div class="ml-3">
                        <div class="text-sm text-green-700">
                            <p>
                                {{ $success }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="sm:mx-auto sm:w-full sm:max-w-sm">
            <form class="space-y-6" wire:submit="submit">
                <div>
                    <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Adresse
                        email</label>
                    <div class="mt-2">
                        <input id="email" name="email" type="email" autocomplete="email" required
                            wire:model="email"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                </div>

                <div>
                    <button type="submit"
                        class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                        Recevoir un lien de connexion
                    </button>
                </div>
            </form>
        </div>
    </div>
