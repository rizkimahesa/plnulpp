<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'PLNULPWayHalim') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('img/logo-pln.png') }}">


    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Custom Styles -->
    <style>
        .logo-img {
            height: 48px;
            width: auto;
        }

        .scrollbar-thin::-webkit-scrollbar {
            height: 6px;
        }

        .scrollbar-thin::-webkit-scrollbar-thumb {
            background-color: rgba(100, 116, 139, 0.4);
            border-radius: 3px;
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200">
    {{-- Navbar --}}
    @auth
    @if (Auth::user()->role === 'admin')
        @include('layouts.navigation')
    @else
        @include('layouts.navigationuser')
    @endif
    @endauth

    <!-- Page Content -->
    <main class="pt-20 px-4 sm:px-6 lg:px-8">
        @yield('content')
    </main>
</body>
</html>
