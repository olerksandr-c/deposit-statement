<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    {{-- <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" /> --}}

    <!-- Scripts -->
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

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        <livewire:layout.navigation />

        <!-- Page Heading -->
        @if (isset($header))
            <header class="bg-white dark:bg-gray-800 shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>
    </div>

    <div x-data="notificationManager" {{-- Инициализируем Alpine компонент --}} @notify.window="addNotification($event.detail)"
        {{-- Слушаем событие 'notify' --}} class="fixed top-5 right-5 z-[100] flex flex-col items-end space-y-3 w-full max-w-xs"
        role="status" aria-live="polite">

        {{-- Перебираем массив уведомлений --}}
        <template x-for="(notification, index) in notifications" :key="notification.id">
            <div x-show="notification.visible" x-transition:enter="transform ease-out duration-300 transition"
                x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-4"
                x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
                x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0 translate-x-4"
                class="relative w-full p-4 border rounded-lg shadow-lg pointer-events-auto"
                :class="{
                    'bg-green-100 border-green-300 text-green-800 dark:bg-green-900/80 dark:border-green-700 dark:text-green-200': notification
                        .type === 'success',
                    'bg-red-100 border-red-300 text-red-800 dark:bg-red-900/80 dark:border-red-700 dark:text-red-200': notification
                        .type === 'error',
                    'bg-blue-100 border-blue-300 text-blue-800 dark:bg-blue-900/80 dark:border-blue-700 dark:text-blue-200': notification
                        .type === 'info',
                    'bg-yellow-100 border-yellow-300 text-yellow-800 dark:bg-yellow-900/80 dark:border-yellow-700 dark:text-yellow-200': notification
                        .type === 'warning'
                }">
                <div class="flex items-start">
                    {{-- Иконка (опционально) --}}
                    <div class="flex-shrink-0">
                        <svg x-show="notification.type === 'success'" class="w-5 h-5 text-green-500" fill="currentColor"
                            viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <svg x-show="notification.type === 'error'" class="w-5 h-5 text-red-500" fill="currentColor"
                            viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <svg x-show="notification.type === 'info'" class="w-5 h-5 text-blue-500" fill="currentColor"
                            viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <svg x-show="notification.type === 'warning'" class="w-5 h-5 text-yellow-500"
                            fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    {{-- Текст сообщения --}}
                    <div class="ml-3 flex-1 pt-0.5">
                        <p class="text-sm font-medium" x-text="notification.message"></p>
                    </div>
                    {{-- Кнопка закрыть --}}
                    <div class="ml-4 flex-shrink-0 flex">
                        <button @click="removeNotification(notification.id)"
                            class="-mr-1 flex p-1 rounded-md opacity-70 hover:opacity-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-transparent"
                            :class="{
                                'hover:bg-green-200 dark:hover:bg-green-800/50': notification.type === 'success',
                                'hover:bg-red-200 dark:hover:bg-red-800/50': notification.type === 'error',
                                'hover:bg-blue-200 dark:hover:bg-blue-800/50': notification.type === 'info',
                                'hover:bg-yellow-200 dark:hover:bg-yellow-800/50': notification.type === 'warning'
                            }">
                            <span class="sr-only">Закрити</span>
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <script>
        // Менеджер уведомлений Alpine.js
        document.addEventListener('alpine:init', () => {
            Alpine.data('notificationManager', () => ({
                notifications: [], // Массив для хранения активных уведомлений
                addNotification(detail) {
                    const id = Date.now() + Math.random(); // Уникальный ID
                    const notification = {
                        id: id,
                        // Обрабатываем разные способы передачи данных из Livewire v3
                        message: detail.message ?? detail[0]?.message ?? 'No message provided',
                        type: detail.type ?? detail[0]?.type ??
                        'info', // 'success', 'error', 'info', 'warning'
                        visible: true,
                        timeout: null // Для хранения ID таймера автоскрытия
                    };

                    this.notifications.push(notification);

                    // Устанавливаем таймер для автоскрытия
                    notification.timeout = setTimeout(() => {
                        this.removeNotification(id);
                    }, 5000); // Скрывать через 5 секунд
                },
                // Метод для удаления уведомления по ID
                removeNotification(id) {
                    const index = this.notifications.findIndex(n => n.id === id);
                    if (index > -1) {
                        // Сначала делаем невидимым для анимации
                        this.notifications[index].visible = false;
                        // Очищаем таймер автоскрытия, если он есть
                        if (this.notifications[index].timeout) {
                            clearTimeout(this.notifications[index].timeout);
                        }
                        // Удаляем из массива после завершения анимации
                        setTimeout(() => {
                            this.notifications.splice(index, 1);
                        }, 300); // Должно быть равно длительности leave-transition
                    }
                }
            }));
        });
    </script>

</body>

</html>
