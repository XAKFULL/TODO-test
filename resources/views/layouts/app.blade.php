<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Канбан</title>

    <!-- Подключаем CSS -->
    @vite(['resources/css/app.css', 'resources/css/tasks.css'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-100">

<div class="container mx-auto py-8">
    @yield('content')
</div>

<!-- Подключаем JS -->
@vite(['resources/css/app.css', 'resources/css/tasks.css', 'resources/js/tasks.js', 'resources/js/tasks-list.js'])
</body>
</html>
