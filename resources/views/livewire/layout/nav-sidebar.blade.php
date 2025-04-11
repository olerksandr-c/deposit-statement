<div>
    @script
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.store('sidebar', {
                    isOpen: localStorage.getItem('sidebarOpen') === 'true' ? true : false,
                    toggle() {
                        this.isOpen = !this.isOpen;
                        localStorage.setItem('sidebarOpen', this.isOpen);
                        Livewire.dispatch('toggleSidebar');
                    },
                    syncState(state) {
                        this.isOpen = state;
                        localStorage.setItem('sidebarOpen', this.isOpen);
                    }
                });
            });
        </script>
    @endscript

    <nav
        class="fixed top-0 z-30 w-full flex items-center bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700 transition-all duration-300 ease-in-out">
        <!-- Логотип и название -->
        <div class="flex items-center h-16 px-4 border-r border-gray-200 dark:border-gray-700 transition-[width] duration-300 ease-in-out"
            :class="{ 'w-64': $store.sidebar.isOpen, 'w-16': !$store.sidebar.isOpen }">
            <a href="/" wire:navigate preserve-state class="flex items-center space-x-2">
                <svg width="32" height="32" viewBox="0 0 32 32" fill="#3B82F6" xmlns="http://www.w3.org/2000/svg">
                    <rect x="4" y="4" width="24" height="24" rx="6" fill="inherit" />
                    <path
                        d="M16 10V22M19 13C19 11.8954 18.1046 11 17 11H15C13.8954 11 13 11.8954 13 13C13 14.1046 13.8954 15 15 15H17C18.1046 15 19 16.1046 19 17C19 18.1046 18.1046 19 17 19H15C13.8954 19 13 19.8954 13 21"
                        stroke="white" stroke-width="2" />
                </svg>
                <span
                    class="text-lg font-semibold text-gray-900 dark:text-white transition-all duration-300 whitespace-nowrap"
                    :class="{ 'opacity-0 w-0 overflow-hidden': !$store.sidebar.isOpen }">
                    {{ config('app.name', 'Laravel') }}
                </span>
            </a>
        </div>

        <div class="flex items-center justify-between flex-1 px-4">
            <!-- Кнопка сворачивания -->
            <div class="flex items-center">
                <button x-on:click="$store.sidebar.toggle()"
                    class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600">
                    <span class="sr-only">Toggle sidebar</span>
                    <svg x-show="$store.sidebar.isOpen" class="w-6 h-6" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    <svg x-show="!$store.sidebar.isOpen" class="w-6 h-6" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>

            <!-- Правые элементы навбара -->


            {{-- Контейнер для dropdown. 'relative' нужен для позиционирования панели --}}


            <div x-data="{ isOpen: false }" class="relative">
                {{-- Кнопка-триггер для открытия/закрытия --}}
                <button type="button" @click="isOpen = !isOpen" @keydown.escape.window="isOpen = false"
                    class="flex text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600"
                    aria-expanded="false" :aria-expanded="isOpen.toString()" aria-haspopup="true"
                    aria-controls="user-dropdown-panel">
                    <span class="sr-only">Відкрити меню користувача</span>
                    <svg class="w-6 h-6 text-gray-50 dark:text-white" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                        viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-width="2"
                            d="M7 17v1a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1v-1a3 3 0 0 0-3-3h-4a3 3 0 0 0-3 3Zm8-9a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                </button>

                {{-- Сама панель dropdown --}}
                <div x-show="isOpen" @click.outside="isOpen = false" x-cloak id="user-dropdown-panel"
                    class="absolute right-0 z-50 my-2 w-56 text-base list-none bg-white divide-y divide-gray-100 rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none dark:bg-gray-700 dark:divide-gray-600"
                    role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button"
                    x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">

                    {{-- Секция с информацией о пользователе --}}
                    <div class="px-4 py-3">
                        <span class="block text-sm text-gray-900 dark:text-white">{{ Auth::user()->name }}</span>
                        <span class="block text-sm font-medium text-gray-500 truncate dark:text-gray-300 w-full">
                            {{ Auth::user()->email }}
                        </span>
                    </div>

                    {{-- Ссылки меню --}}
                    <ul class="py-1" role="none">
                        <li>
                            <a href="{{ route('profile') }}" @if (class_exists('\Livewire\Features\SupportNavigateAttribute\Navigatable')) wire:navigate @endif
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white"
                                role="menuitem" @click="isOpen = false">
                                {{ __('Profile') }}
                            </a>
                        </li>
                        <li>
                            <button wire:click="logout"
                                class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white"
                                role="menuitem" @click="isOpen = false">
                                {{ __('Log Out') }}
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Сайдбар (Боковая панель) -->
    <aside id="logo-sidebar"
        class="fixed top-0 left-0 z-20 h-screen pt-20 bg-white border-r border-gray-200 dark:bg-gray-800 dark:border-gray-700 transition-[width] duration-300 ease-in-out"
        :class="{ 'w-64': $store.sidebar.isOpen, 'w-16': !$store.sidebar.isOpen }" aria-label="Sidebar">

        <div class="h-full px-3 pb-4 overflow-y-auto overflow-x-hidden bg-white dark:bg-gray-800">
            <ul class="space-y-2 font-medium">
                <li>
                    <a href="{{ route('bank') }}" wire:navigate preserve-state x-on:click="$event.stopPropagation()"
                        class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group"
                        x-bind:data-tooltip-disabled="$store.sidebar.isOpen">
                        <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                            viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 10V4a1 1 0 0 0-1-1H9.914a1 1 0 0 0-.707.293L5.293 7.207A1 1 0 0 0 5 7.914V20a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2M10 3v4a1 1 0 0 1-1 1H5m5 6h9m0 0-2-2m2 2-2 2" />
                        </svg>
                        <span class="ms-3 whitespace-nowrap" :class="{ 'hidden': !$store.sidebar.isOpen }">Депозитна
                            виписка</span>
                    </a>
                </li>
                @role('administrator')
                    <li>
                        <a href="{{ route('logs') }}" wire:navigate preserve-state
                            class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group"
                            x-bind:data-tooltip-disabled="$store.sidebar.isOpen">
                            <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="m8 9 3 3-3 3m5 0h3M4 19h16a1 1 0 0 0 1-1V6a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1Z" />
                            </svg>
                            <span class="flex-1 ms-3 whitespace-nowrap"
                                :class="{ 'hidden': !$store.sidebar.isOpen }">Логування</span>
                            <span
                                class="inline-flex items-center justify-center px-2 ms-3 text-sm font-medium text-gray-800 bg-gray-100 rounded-full dark:bg-gray-700 dark:text-gray-300"
                                :class="{ 'hidden': !$store.sidebar.isOpen }">Admin</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('role-permission-manager') }}" wire:navigate preserve-state
                            class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group"
                            x-bind:data-tooltip-disabled="$store.sidebar.isOpen">
                            <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                                    d="M4.5 17H4a1 1 0 0 1-1-1 3 3 0 0 1 3-3h1m0-3.05A2.5 2.5 0 1 1 9 5.5M19.5 17h.5a1 1 0 0 0 1-1 3 3 0 0 0-3-3h-1m0-3.05a2.5 2.5 0 1 0-2-4.45m.5 13.5h-7a1 1 0 0 1-1-1 3 3 0 0 1 3-3h3a3 3 0 0 1 3 3 1 1 0 0 1-1 1Zm-1-9.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Z" />
                            </svg>
                            <span class="flex-1 ms-3 whitespace-nowrap" :class="{ 'hidden': !$store.sidebar.isOpen }">Ролі
                                та права</span>
                            <span
                                class="inline-flex items-center justify-center px-2 ms-3 text-sm font-medium text-gray-800 bg-gray-100 rounded-full dark:bg-gray-700 dark:text-gray-300"
                                :class="{ 'hidden': !$store.sidebar.isOpen }">Admin</span>
                        </a>
                    </li>
                @endrole
                <li>
                    <a href="/docs/manual.pdf" target="_blank"
                        class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group"
                        x-bind:data-tooltip-disabled="$store.sidebar.isOpen">
                        <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                            viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2"
                                d="M9.529 9.988a2.502 2.502 0 1 1 5 .191A2.441 2.441 0 0 1 12 12.582V14m-.01 3.008H12M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        <span class="flex-1 ms-3 whitespace-nowrap"
                            :class="{ 'hidden': !$store.sidebar.isOpen }">Інструкція</span>
                    </a>
                </li>
            </ul>
        </div>
    </aside>
</div>
