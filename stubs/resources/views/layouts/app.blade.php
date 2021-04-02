<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        University of Illinois Springfield - UIS - {{ env('APP_NAME') }} - {{ $title ?? env('APP_NAME') }}
    </title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@5.x/css/materialdesignicons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Material+Icons" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ url(mix('css/app.css')) }}">
@if($styles ?? '')
    {{ $styles }}
@endif
<!-- Scripts -->
    <script src="{{ url(mix('js/app.js')) }}" defer></script>

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-125366978-1" defer></script>
    <script defer>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'UA-125366978-1');
    </script>
</head>

<body class="font-sans antialiased">
<div class="flex flex-col min-h-screen bg-gray-100">
    <div class="flex-grow">
        <x-navigation-menu></x-navigation-menu>
        <div id="app">
            <v-app data-app>

                <!-- Page Heading -->
                @if($header ?? '')
                    <header class="bg-white shadow">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endif

                @if($errors)
                    <x-errors></x-errors>
            @endif

            <!-- Page Content -->
                <main class="bg-gray-100">
                    {{ $slot }}
                </main>

            </v-app>
        </div>
    </div>

    <x-footer></x-footer>
</div>
@if($scripts ?? '')
    {{ $scripts }}
@endif
<!--OneTrust Cookies Consent Notice (Production CDN, uis.edu, en-GB) start -->
<script src="https://cdn.cookielaw.org/consent/3ca42bb6-c1b2-4c5d-9e95-b3c10f01a06c.js" type="text/javascript" charset="UTF-8" defer></script>
<script type="text/javascript" defer>function OptanonWrapper(){}</script>
<!--OneTrust Cookies Consent Notice (Production CDN, uis.edu, en-GB) end -->
</body>
</html>
