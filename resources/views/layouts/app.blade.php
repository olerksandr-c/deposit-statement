<!DOCTYPE html>
{{-- Додаємо x-data для ініціалізації Alpine та змінної isSidebarOpen --}}
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ isSidebarOpen: true }">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Переконайтесь, що AlpineJS включено у ваш build процес в app.js --}}
    {{-- Переконайтесь, що Flowbite JS ініціалізується в app.js --}}

</head>

<body class="bg-gray-50 dark:bg-gray-900">

    {{-- Рендеримо Livewire компонент. --}}
    {{-- Тепер він НЕ приймає слоти, а лише рендерить nav та aside --}}
    {{-- Ми передаємо стан isSidebarOpen з Alpine в компонент через :is-sidebar-open --}}
    {{-- Цей атрибут буде доступний у Livewire через mount() або updated() --}}
    {{-- АБО простіше - Livewire компонент сам керує своїм станом, а ми його синхронізуємо з Alpine --}}
    <livewire:layout.nav-sidebar />

    {{-- Основний контент --}}
    {{-- Додаємо динамічний клас для відступу зліва на основі стану isSidebarOpen з Alpine --}}
    {{-- 'transition-all' для плавності --}}
    <main class="p-4 transition-all duration-300 ease-in-out"
        :class="{ 'sm:ml-64': $store.sidebar.isOpen, 'sm:ml-16': !$store.sidebar.isOpen }">
        <div class="mt-14">
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
    {{-- Ініціалізація Alpine Store для глобального стану сайдбару --}}
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('sidebar', {
                isOpen: true, // Початковий стан

                toggle() {
                    this.isOpen = !this.isOpen
                },

                // Функція для синхронізації з Livewire
                syncState(isOpen) {
                    if (this.isOpen !== isOpen) {
                        this.isOpen = isOpen;
                    }
                }
            })
        })
    </script>

</body>

</html>
