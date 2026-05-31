<!DOCTYPE html>
<html lang="ms" data-vibe="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'InPreS'))</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('head')
</head>
<body>
    {{ $slot ?? '' }}
    @yield('body')

    @stack('scripts')
</body>
</html>
