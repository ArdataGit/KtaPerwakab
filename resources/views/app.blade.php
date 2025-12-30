<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'App' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])


    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet"
        crossorigin="anonymous" />

    @livewireStyles
</head>

<body class="bg-gray-200">

    {{ $slot }}

    @livewireScripts
</body>

</html>