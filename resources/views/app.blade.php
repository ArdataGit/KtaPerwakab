<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'App' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- PAKSA LIGHT MODE -->
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
  <meta name="viewport"
      content="width=device-width, initial-scale=1.0, viewport-fit=cover">


    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet"
        crossorigin="anonymous" />

    @livewireStyles
</head>

<body class="bg-gray-200">

    {{ $slot }}

    @livewireScripts
    <script>
      if (window.Capacitor?.isNativePlatform?.()) {
        document.documentElement.classList.add('capacitor');
      }
    </script>

</body>

</html>