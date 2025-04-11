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

    <script>
        // Выполняем немедленно для предотвращения мерцания при загрузке
        if (localStorage.getItem('darkMode') === 'true' ||
            (localStorage.getItem('darkMode') === null &&
                window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>

<body class="bg-gray-50 dark:bg-gray-900">

    <livewire:layout.nav-sidebar />

    {{-- Основний контент --}}

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

    <div x-data="notificationManager" @notify.window="addNotification($event.detail)"
        class="fixed top-5 right-5 z-[100] flex flex-col items-end space-y-3 w-full max-w-xs" role="status"
        aria-live="polite">

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
                                d="M8.257 3.099c.62-.62 1.637-.62 2.257 0l6.087 6.087a1.6 1.6 0 01-.001 2.258l-6.087 6.087a1.6 1.6 0 01-2.257 0L2.17 11.444a1.6 1.6 0 01-.001-2.258L8.257 3.1zM10 14a1 1 0 100-2 1 1 0 000 2zm0-6a1 1 0 110 2H9a1 1 0 110-2h1z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3 flex-1 pt-0.5">
                        <p class="text-sm font-medium" x-text="notification.message"></p>
                    </div>
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
                            {{-- Іконка закриття (скорочено) --}}
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
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

            // 🔔 Компонент для сповіщень (без змін)
            Alpine.data('notificationManager', () => ({
                notifications: [],
                addNotification(detail) {
                    const id = Date.now() + Math.random();
                    // Покращена обробка вкладених деталей, якщо Livewire надсилає масив
                    const message = typeof detail === 'string' ? detail : (detail.message ?? detail[0]
                        ?.message ?? 'Немає повідомлення');
                    const type = typeof detail === 'string' ? 'info' : (detail.type ?? detail[0]
                        ?.type ?? 'info');

                    const notification = {
                        id,
                        message: message,
                        type: type,
                        visible: true,
                        timeout: null
                    };
                    this.notifications.push(notification);
                    notification.timeout = setTimeout(() => {
                        this.removeNotification(id);
                    }, 5000);
                },
                removeNotification(id) {
                    const index = this.notifications.findIndex(n => n.id === id);
                    if (index > -1) {
                        // Спочатку робимо невидимим для анімації
                        this.notifications[index].visible = false;
                        if (this.notifications[index].timeout) {
                            clearTimeout(this.notifications[index].timeout);
                        }
                        // Видаляємо з масиву після завершення анімації
                        setTimeout(() => {
                            // Перевіряємо ще раз, чи елемент все ще існує перед видаленням
                            const currentIndex = this.notifications.findIndex(n => n.id === id);
                            if (currentIndex > -1) {
                                this.notifications.splice(currentIndex, 1);
                            }
                        }, 300); // Час має відповідати тривалості анімації x-transition:leave
                    }
                }
            }));


        })
    </script>


</body>

</html>
