 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <meta name="apple-mobile-web-app-capable" content="yes">
 <meta name="apple-touch-fullscreen" content="yes">
 <meta name="apple-mobile-web-app-status-bar-style" content="default">

 <meta name="apple-mobile-web-app-capable" content="yes">
 <meta name="apple-touch-fullscreen" content="yes">
 <meta name="apple-mobile-web-app-status-bar-style" content="default">

 <!-- CSRF Token -->
 <meta name="csrf-token" content="{{ csrf_token() }}">
 <meta name="baseUrl" content="{{ config('app.url') }}">
 <meta name="userId" content="{{ Auth::check() ? Auth::user()->id : '' }}">

 <title>{{ config('app.name', 'Laravel') }}</title>

 <!-- Scripts -->
 <script src="{{ mix('js/app.js') }}" defer></script>

 <!-- Fonts -->
 <link rel="dns-prefetch" href="//fonts.gstatic.com">
 <!-- <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet"> -->

 <!-- Styles -->
 <link href="{{ mix('css/app.css') }}" rel="stylesheet">
 <link href="{{ mix('css/theme.css') }}" rel="stylesheet">