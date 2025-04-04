<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Депозитна виписка</title>
    <!-- Подключение Bootstrap CSS -->
    {{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> --}}



    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        // On page load or when changing themes, best to add inline in `head` to avoid FOUC
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia(
                '(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>
</head>

<body class="dark:bg-gray-400">

    <div>
        <!-- Включаем navbar -->
        @include('partials.navbar')

        <div class="container mt-5">
            {{ $slot }} <!-- Сюда будет подставляться содержимое вашего блейда -->
        </div>

        <!-- Footer -->
        @include('partials.footer')

    </div>

</body>

</html>
