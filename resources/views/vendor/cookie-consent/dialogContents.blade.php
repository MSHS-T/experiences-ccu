<div class="js-cookie-consent cookie-consent fixed bottom-0 inset-x-0 flex justify-center lg:justify-end pb-2 text-sm">
    <div class="max-w-lg w-full px-6">
        <div class="p-2 rounded-lg bg-blue-100">
            <div class="flex items-center justify-between flex-wrap">
                <div class="w-0 flex-1 items-center hidden md:inline">
                    <p class="ml-3 text-black cookie-consent__message text-justify mr-2">
                        {!! trans('cookie-consent::texts.message') !!}
                    </p>
                </div>
                <div class="mt-2 shrink-0 w-full sm:mt-0 sm:w-auto mx-1">
                    <button
                        class="js-cookie-consent-agree cookie-consent__agree cursor-pointer flex items-center justify-center px-2 py-1 rounded-md text-xs font-medium text-blue-800 bg-blue-400 hover:bg-blue-300">
                        {{ trans('cookie-consent::texts.agree') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
