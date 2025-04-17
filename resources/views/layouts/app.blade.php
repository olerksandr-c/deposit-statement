<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{
    sidebarOpen: window.innerWidth >= 1024 ? JSON.parse(localStorage.getItem('sidebarOpen') ?? 'true') : false,
    darkMode: localStorage.getItem('darkMode') === 'true',
    isMobile: window.innerWidth < 1024,
    dropdownOpen: false
}" x-init="// Сохраняем тему при изменении
$watch('darkMode', val => localStorage.setItem('darkMode', val));
$watch('sidebarOpen', val => localStorage.setItem('sidebarOpen', val));

if (darkMode) document.documentElement.classList.add('dark');

// Обработчик изменения размера окна
window.addEventListener('resize', () => {
    isMobile = window.innerWidth < 1024;
    if (!isMobile) {
        sidebarOpen = JSON.parse(localStorage.getItem('sidebarOpen') ?? 'true');
    } else {
        sidebarOpen = false;
    }
});"
    x-bind:class="{ 'dark': darkMode }">


<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Default Title') - {{ config('app.name', 'Банк') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 antialiased">

    <div class="flex min-h-screen w-full flex-row-reverse overflow-x-clip">

        {{-- Сайдбар --}}
        <livewire:layout.sidebar-component />

        {{-- Основной контент --}}
        <main id="app-content" x-cloak class=" flex-1 transition-all duration-75 ease-in-out "
            :class="sidebarOpen && !isMobile ? 'ml-64' : 'ml-0 lg:ml-20'">
            {{-- Навбар --}}
            <livewire:layout.navbar-component />

            <div class="p-6">
                @if (isset($header))
                    <header class="bg-white dark:bg-gray-800 shadow">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endif


                {{ $slot }}
            </div>
        </main>
    </div>
    <livewire:notifications />
</body>

</html>
