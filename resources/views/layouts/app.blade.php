<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('includes.head')
</head>

@auth
    <body data-col="2-columns" class="2-columns">
        <div id="app">
            @include('includes.header')
            @yield('content')
            @include('includes.footer')
@else
    <body data-col="1-column" class="1-column blank-page">
        <div id="app">
        <main class="py-4">
            @yield('content')
        </main>
@endauth
            @include('includes.footer')
        </div>
    </body>

</html>