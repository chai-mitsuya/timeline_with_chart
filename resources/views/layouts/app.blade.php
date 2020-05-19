<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'TimeLineFree') }}</title>

    <!-- Scripts -->
    <script src="{{ mix('js/app.js') }}" defer></script>
    <script src='/vendor/remodal/remodal.min.js' defer></script>
    <script src='/vendor/c3js/d3.min.js' defer></script>
    <script src='/vendor/c3js/c3.min.js' defer></script>
    <script src='/vendor/colorbox/jquery.colorbox-min.js' defer></script>
    <script src='/vendor/semantic/semantic.min.js' defer></script>
    <script src="{{ mix('/js/twitterLoader.js') }}" defer type="text/javascript"></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href='/vendor/remodal/remodal.css' rel="stylesheet">
    <link href='/vendor/remodal/remodal-default-theme.css' rel="stylesheet">
    <link href='/vendor/c3js/c3.css' rel="stylesheet">
    <link href='/vendor/colorbox/colorbox.css' rel="stylesheet">
    <link href='/vendor/semantic/semantic.min.css' rel="stylesheet">
    <link href="{{ asset('css/common.css') }}" rel="stylesheet">

</head>
<body>
    <div id="app">
        <nav class="navbar navbar-dark bg-dark">
            <a href="#" class="navbar-brand">@lang('title.app_title')</a>
            <div class="border rounded border-success">
                <a class="btn-twitter-link" data-remodal-target="twitter_search_modal">@lang('title.search')</a>
            </div>
        </nav>
        <main class="py-4">
            @yield('content')
        </main>
    </div>
</body>
</html>
