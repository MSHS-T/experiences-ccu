<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    {{-- <meta charset="utf-8">
    <meta name="viewport" content="minimum-scale=1, initial-scale=1, width=device-width, shrink-to-fit=no">

    <title>Expériences CCU</title> --}}

    <link rel="stylesheet" href="{{ mix('/css/app.css') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="icon" media="(prefers-color-scheme:dark)" href="{{ asset('favicon-dark-mode.png')}}" type="image/png" />
    <link rel="icon" media="(prefers-color-scheme:light)" href="{{ asset('favicon.png')}}" type="image/png" />
</head>

<body>
    <div id="app"></div>
    <script type="text/javascript">
        // Pass settings from Laravel
        APP_SETTINGS = JSON.parse('{!! json_encode($settings) !!}');
    </script>
    <script src="{{ mix('/js/app.js') }}"></script>
</body>

</html>