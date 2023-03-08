<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

        <!-- Styles -->
        <link rel="stylesheet" href="http://192.168.0.53/rtddigitalmedia.com/public/app.css">

        <!-- Scripts -->
        @routes
        <script src="http://192.168.0.53/rtddigitalmedia.com/public/js/app.js" defer></script>
    </head>
    <body class="font-sans antialiased">
        @inertia

        @env ('local')
            <script src="http://192.168.0.53/rtddigitalmedia.com/public/js/bundle.js"></script>
        @endenv
    </body>
</html>