{{-- resources/views/components/layout/app.blade.php или ваш layout файл --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{
    // Читаем состояние сайдбара из localStorage, по умолчанию true (открыт)
    // Используем JSON.parse для корректной обработки boolean 'true'/'false' строк
    sidebarOpen: JSON.parse(localStorage.getItem('sidebarOpen') ?? 'true'),
    darkMode: localStorage.getItem('darkMode') === 'true' // Тема остается как была
}" x-init="// Сохраняем тему при изменении
$watch('darkMode', val => localStorage.setItem('darkMode', val));
// ДОБАВЛЕНО: Сохраняем состояние сайдбара при изменении
$watch('sidebarOpen', val => localStorage.setItem('sidebarOpen', val));

// Применяем класс dark при инициализации (если нужно)
if (darkMode) document.documentElement.classList.add('dark');"
    x-bind:class="{ 'dark': darkMode }">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Page Title' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body class="bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 antialiased">
    <div class="flex h-screen overflow-hidden">

        {{-- Сайдбар --}}
        <aside id="app-sidebar" x-cloak
            class="fixed inset-y-0 left-0 z-30 flex-shrink-0 overflow-y-auto bg-white dark:bg-gray-800 transition-all duration-300 ease-in-out"
            :class="sidebarOpen ? 'w-64' : 'w-20'" {{-- Динамическая ширина --}}>
            <div class="flex flex-col h-full">
                {{-- Логотип/Заголовок сайдбара --}}
                <div class="h-16 flex items-center justify-center border-b dark:border-gray-700">
                    <span x-show="sidebarOpen" class="text-xl font-semibold dark:text-white">Ваш Лого</span>
                    <span x-show="!sidebarOpen" class="text-xl font-semibold dark:text-white">Л</span>
                    {{-- Иконка/короткий текст --}}
                </div>

                {{-- Навигация --}}
                <nav class="flex-1 px-4 py-4 space-y-2">
                    <a href="/bank"
                        class="flex items-center px-2 py-2 text-sm font-medium rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 group">
                        <svg class="h-6 w-6 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor"> {{-- Иконка --}}
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span x-show="sidebarOpen" class="transition-opacity duration-200"
                            :class="{ 'opacity-0': !sidebarOpen }">Дашборд</span>
                    </a>
                    <a href="/logs"
                        class="flex items-center px-2 py-2 text-sm font-medium rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 group">
                        <svg class="h-6 w-6 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor"> {{-- Иконка --}}
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M15 21v-1a6 6 0 00-5.173-5.924M12 12a4 4 0 110-8 4 4 0 010 8z" />
                        </svg>
                        <span x-show="sidebarOpen" class="transition-opacity duration-200"
                            :class="{ 'opacity-0': !sidebarOpen }">Пользователи</span>
                    </a>
                    {{-- Другие пункты меню --}}
                </nav>

                {{-- Футер сайдбара (кнопки) --}}
                <div class="px-4 py-3 border-t dark:border-gray-700 space-y-2">
                    {{-- Кнопка Свернуть/Развернуть --}}
                    <button @click="sidebarOpen = !sidebarOpen"
                        class="w-full flex items-center justify-center px-2 py-2 text-sm font-medium rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 group">
                        <svg x-show="sidebarOpen" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                        </svg>
                        <svg x-show="!sidebarOpen" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                        </svg>
                        <span class="sr-only">Свернуть/Развернуть сайдбар</span> {{-- Для доступности --}}
                    </button>

                    {{-- Кнопка Тема --}}
                    <button @click="darkMode = !darkMode; document.documentElement.classList.toggle('dark', darkMode)"
                        class="w-full flex items-center justify-center px-2 py-2 text-sm font-medium rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 group">
                        <svg x-show="!darkMode" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor"> {{-- Иконка Солнце --}}
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <svg x-show="darkMode" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            {{-- Иконка Луна --}}
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                        <span class="sr-only">Переключить тему</span> {{-- Для доступности --}}
                    </button>
                </div>
            </div>
        </aside>

        {{-- Основной контент --}}
        <main id="app-content" x-cloak class="flex-1 overflow-y-auto transition-all duration-300 ease-in-out"
            :class="sidebarOpen ? 'ml-64' : 'ml-20'" {{-- Динамический отступ --}}>
            <div class="p-6">
                {{-- Кнопка-бургер для мобильных (если нужна) --}}
                {{-- <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden mb-4">...</button> --}}

                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Заголовок Страницы</h1>
                {{-- Сюда будет вставляться контент страницы через $slot --}}
                {{ $slot }}
            </div>
        </main>

    </div>
</body>

</html>
