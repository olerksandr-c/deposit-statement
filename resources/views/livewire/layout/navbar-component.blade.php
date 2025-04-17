{{-- Верхний навбар --}}
<nav
    class="sticky top-0 z-20 w-full flex items-center h-16 bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700">
    <div class="flex items-center justify-between w-full px-4">
        {{-- Левая часть навбара --}}
        <div class="flex items-center">
            {{-- Бургер меню для мобильных --}}
            <button x-show="isMobile" @click="sidebarOpen = !sidebarOpen"
                class="p-2 mr-2 rounded-md text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            {{-- Кнопка сворачивания сайдбара для десктопа --}}
            <button x-show="!isMobile" @click="sidebarOpen = !sidebarOpen"
                class="p-2 mr-2 rounded-md text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                <svg x-show="sidebarOpen" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                </svg>
                <svg x-show="!sidebarOpen" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                </svg>
            </button>

            {{-- <span class="text-xl font-semibold dark:text-white">
            {{ config('app.name', 'Laravel') }}
        </span> --}}
        </div>

        {{-- Правая часть навбара --}}
        <div class="flex items-center space-x-4">
            {{-- Кнопка переключения темы --}}
            <button @click="darkMode = !darkMode; document.documentElement.classList.toggle('dark', darkMode)"
                class="p-2 rounded-md text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                <svg x-show="darkMode" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <svg x-show="!darkMode" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                </svg>
            </button>

            {{-- Dropdown пользователя --}}
            <div class="relative">
                <button type="button" @click="dropdownOpen = !dropdownOpen"
                    @keydown.escape.window="dropdownOpen = false"
                    class="flex text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600"
                    aria-expanded="false" :aria-expanded="dropdownOpen.toString()" aria-haspopup="true">
                    <span class="sr-only">Открыть меню пользователя</span>
                    <svg class="w-6 h-6 text-gray-50 dark:text-white" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                        viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-width="2"
                            d="M7 17v1a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1v-1a3 3 0 0 0-3-3h-4a3 3 0 0 0-3 3Zm8-9a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                </button>

                <div x-show="dropdownOpen" @click.outside="dropdownOpen = false" x-cloak
                    class="absolute right-0 z-50 my-2 w-56 text-base list-none bg-white divide-y divide-gray-100 rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none dark:bg-gray-700 dark:divide-gray-600"
                    role="menu" x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95">

                    <div class="px-4 py-3">
                        <span class="block text-sm text-gray-900 dark:text-white">{{ Auth::user()->name }}</span>
                        <span class="block text-sm font-medium text-gray-500 truncate dark:text-gray-300">
                            {{ Auth::user()->email }}
                        </span>
                    </div>

                    <ul class="py-1" role="none">
                        <li>
                            <a href="{{ route('profile') }}" @if (class_exists('\Livewire\Features\SupportNavigateAttribute\Navigatable')) wire:navigate @endif
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white"
                                role="menuitem" @click="dropdownOpen = false">
                                {{ __('Profile') }}
                            </a>
                        </li>
                        <li>
                            <button wire:click="logout"
                                class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white"
                                role="menuitem" @click="dropdownOpen = false">
                                {{ __('Log Out') }}
                            </button>
                        </li>

                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>
