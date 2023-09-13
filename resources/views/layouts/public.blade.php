<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title') | {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossOrigin="anonymous" />
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Lexend:wght@400;500&display=swap" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="flex flex-col h-full min-h-screen">

    <header class="py-4 h-[5vh]">
        <nav class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 relative z-50 flex justify-between items-center">
            <div class="flex items-center md:gap-x-12 font-bold tracking-wide text-xl">
                <a aria-label="Home" href="{{ route('home') }}">
                    {{ config('app.name') }}
                </a>
            </div>
            <div class="flex items-center gap-x-5 md:gap-x-8">

                <a class="group inline-flex items-center justify-center rounded-full py-2 px-4 text-sm font-semibold focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 bg-blue-600 text-white hover:text-slate-100 hover:bg-blue-500 active:bg-blue-800 active:text-blue-100 focus-visible:outline-blue-600"
                    href="/mes-inscriptions">
                    Mes inscriptions
                </a>
            </div>
        </nav>
    </header>

    <main class="flex-grow">
        @yield('content')
    </main>

    <footer class="bg-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="py-4">
                <img alt="Université Toulouse-Jean Jaurès" class="h-36 w-auto mx-auto block"
                    src="{{ Vite::asset('resources/images/logo-ccu-horizontal.jpg') }}">
            </div>
            <div class="grid lg:grid-cols-3 items-center border-t border-slate-400/10 py-4 space-y-4 lg:space-y-0">
                <p class="text-center sm:text-left text-sm text-slate-500 sm:mt-0">
                    Copyright © 2023 Expériences CCU
                </p>
                <div class="flex justify-center gap-x-6">
                    <a class="inline-block rounded-lg px-2 py-1 text-sm text-slate-700 hover:bg-slate-100 hover:text-slate-900"
                        href="{{ route('cookies') }}">Politique de Cookies</a>
                    <a class="inline-block rounded-lg px-2 py-1 text-sm text-slate-700 hover:bg-slate-100 hover:text-slate-900"
                        href="{{ route('legal') }}">Mentions Légales</a>
                </div>
                <a class="text-center sm:text-right text-sm text-slate-500 sm:mt-0" href="https://3rgo.tech"
                    target="_blank">
                    Réalisé par 3rgo.tech
                </a>
            </div>
        </div>
    </footer>
</body>

</html>
