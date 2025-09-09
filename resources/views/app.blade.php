<!-- resources/views/app.blade.php -->
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- @vite('resources/js/app.js') -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @inertiaHead
    @filamentStyles
</head>

<body class="antialiased">
    @inertia
    <script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('open-url', ({
            url
        }) => window.open(url, '_blank'));
    });
    </script>
    @filamentScripts
</body>

</html>