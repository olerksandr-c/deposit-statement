<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;
use Livewire\Attributes\Reactive; // Імпортуємо атрибут Reactive

new class extends Component {
    /**
     * Змінна для відстеження стану сайдбару
     */
    public bool $isSidebarCollapsed = false;

    /**
     * Змінна для відстеження стану навігації Livewire.
     * Атрибут #[Reactive] автоматично оновить цю властивість,
     * коли зміниться isNavigating у батьківському компоненті/шаблоні.
     */
    #[Reactive]
    public bool $isNavigating = false;

    /**
     * Ініціалізація стану з localStorage через Alpine x-init у шаблоні
     * Залишається, як є, бо відбувається на клієнті
     */

    /**
     * Змінює стан сайдбару між звичайним та згорнутим
     */
    public function toggleSidebar(): void
    {
        $this->isSidebarCollapsed = !$this->isSidebarCollapsed;
        $this->js('localStorage.setItem("sidebarCollapsed", "' . ($this->isSidebarCollapsed ? 'true' : 'false') . '")');
        $this->dispatch('sidebar-toggle', collapsed: $this->isSidebarCollapsed); // Подія для Alpine у app.blade.php
    }

    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();
        $this->redirect('/', navigate: true);
    }
}; ?>

<div>
    <!-- Весь контент в одному div для Livewire компонента -->
    <div class="flex">
        {{-- Сайдбар --}}
        <aside id="logo-sidebar" {{-- Ініціалізуємо isSidebarCollapsed з localStorage при першому завантаженні --}} x-data="{
            init() {
                const saved = localStorage.getItem('sidebarCollapsed') === 'true';
                // Встановлюємо початкове значення в Livewire
                $wire.set('isSidebarCollapsed', saved);
                // Також повідомляємо Alpine у app.blade.php про початковий стан
                window.dispatchEvent(new CustomEvent('sidebar-state-updated', { detail: { collapsed: saved } }));
            }
        }" x-init="init()"
            class="fixed top-0 left-0 z-40 h-screen bg-white border-r border-gray-200 dark:bg-gray-900 dark:border-gray-700"
            {{-- Використовуємо $wire.isNavigating для керування переходами --}}
            :class="{
                'w-20': $wire.isSidebarCollapsed,
                'w-64': !$wire.isSidebarCollapsed,
                '-translate-x-full': false,
                /* Твоя логіка для мобільних */
                'sm:translate-x-0': true,
                'transition-transform': !$wire.isNavigating,
                /* Анімація для мобільних */
                'transition-width': !$wire.isNavigating,
                /* ТІЛЬКИ коли не навігуємо */
                'duration-300': !$wire.isNavigating,
                /* ТІЛЬКИ коли не навігуємо */
                'duration-0': $wire.isNavigating /* Миттєво під час навігації */
            }"
            aria-label="Sidebar">
            <div class="h-full px-3 pb-4 overflow-y-auto bg-white dark:bg-gray-900 flex flex-col justify-between">
                {{-- Верхня частина: логотип та пункти меню --}}
                <div>
                    <div class="flex items-center ps-2.5 pt-5 mb-5">
                        {{-- Логотип --}}
                        <a href="{{ config('app.url') }}" class="flex items-center" wire:navigate> {{-- Додав wire:navigate сюди теж для консистентності --}}
                            <svg width="32" height="32" viewBox="0 0 32 32" fill="#3B82F6"
                                xmlns="http://www.w3.org/2000/svg">
                                <rect x="4" y="4" width="24" height="24" rx="6" fill="inherit" />
                                <path
                                    d="M16 10V22M19 13C19 11.8954 18.1046 11 17 11H15C13.8954 11 13 11.8954 13 13C13 14.1046 13.8954 15 15 15H17C18.1046 15 19 16.1046 19 17C19 18.1046 18.1046 19 17 19H15C13.8954 19 13 19.8954 13 21"
                                    stroke="white" stroke-width="2" />
                            </svg>
                            <span class="self-center text-xl font-semibold whitespace-nowrap dark:text-white ms-3"
                                x-show="!$wire.isSidebarCollapsed">Банк</span>
                        </a>
                    </div>

                    <ul class="space-y-2 font-medium">
                        <li>
                            <a href="{{ route('bank') }}" @class([
                                'flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700',
                                'bg-blue-100 dark:bg-blue-900' => request()->routeIs('bank'),
                            ])
                                :class="{ 'justify-center': $wire.isSidebarCollapsed }"
                                @if (class_exists('\Livewire\Features\SupportNavigateAttribute\Navigatable')) wire:navigate @endif>
                                <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                    viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 10V4a1 1 0 0 0-1-1H9.914a1 1 0 0 0-.707.293L5.293 7.207A1 1 0 0 0 5 7.914V20a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2M10 3v4a1 1 0 0 1-1 1H5m5 6h9m0 0-2-2m2 2-2 2" />
                                </svg>

                                <span class="ms-3" x-show="!$wire.isSidebarCollapsed">Депозитна виписка</span>
                            </a>
                        </li>

                        @role('administrator')
                            <li>
                                <a href="{{ route('logs') }}" @class([
                                    'flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700',
                                    'bg-blue-100 dark:bg-blue-900' => request()->routeIs('logs'),
                                ])
                                    :class="{ 'justify-center': $wire.isSidebarCollapsed }"
                                    @if (class_exists('\Livewire\Features\SupportNavigateAttribute\Navigatable')) wire:navigate @endif>
                                    <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                        viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2"
                                            d="m8 9 3 3-3 3m5 0h3M4 19h16a1 1 0 0 0 1-1V6a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1Z" />
                                    </svg>
                                    <span class="ms-3" x-show="!$wire.isSidebarCollapsed">Логування</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('role-permission-manager') }}" @class([
                                    'flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700',
                                    'bg-blue-100 dark:bg-blue-900' => request()->routeIs(
                                        'role-permission-manager'),
                                ])
                                    :class="{ 'justify-center': $wire.isSidebarCollapsed }"
                                    @if (class_exists('\Livewire\Features\SupportNavigateAttribute\Navigatable')) wire:navigate @endif>
                                    <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                        viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                                            d="M4.5 17H4a1 1 0 0 1-1-1 3 3 0 0 1 3-3h1m0-3.05A2.5 2.5 0 1 1 9 5.5M19.5 17h.5a1 1 0 0 0 1-1 3 3 0 0 0-3-3h-1m0-3.05a2.5 2.5 0 1 0-2-4.45m.5 13.5h-7a1 1 0 0 1-1-1 3 3 0 0 1 3-3h3a3 3 0 0 1 3 3 1 1 0 0 1-1 1Zm-1-9.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Z" />
                                    </svg>

                                    <span class="ms-3" x-show="!$wire.isSidebarCollapsed">Ролі та права</span>
                                </a>
                            </li>
                        @endrole

                        <li>
                            <a href="/docs/manual.pdf" target="_blank"
                                class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700"
                                :class="{ 'justify-center': $wire.isSidebarCollapsed }">
                                <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                    viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9.529 9.988a2.502 2.502 0 1 1 5 .191A2.441 2.441 0 0 1 12 12.582V14m-.01 3.008H12M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>

                                <span class="ms-3" x-show="!$wire.isSidebarCollapsed">Інструкція</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </aside>

        <!-- Контейнер для навбара -->
        <nav class="bg-white border-b border-gray-200 px-4 py-2.5 dark:bg-gray-800 dark:border-gray-700 fixed top-0 right-0 z-30"
            :class="{
                'left-20': $wire.isSidebarCollapsed,
                'left-64': !$wire
                    .isSidebarCollapsed,
                'left-0': true,
                'sm:left-20': $wire.isSidebarCollapsed,
                'sm:left-64': !$wire
                    .isSidebarCollapsed
            }">
            <div class="flex justify-between items-center">
                <!-- Кнопка для згортання/розгортання в навбарі (видима і на мобільних і на десктопі) -->
                <button type="button" wire:click="toggleSidebar"
                    class="p-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 transition-colors">
                    <span class="sr-only">Згорнути/розгорнути меню</span>
                    <svg x-show="!$wire.isSidebarCollapsed" class="w-5 h-5 text-gray-600 dark:text-gray-400"
                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 12H4m0 0 6-6m-6 6 6 6" />
                    </svg>
                    <svg x-show="$wire.isSidebarCollapsed" class="w-5 h-5 text-gray-600 dark:text-gray-400"
                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 12h16m0 0-6-6m6 6-6 6" />
                    </svg>
                </button>

                <!-- Назва сторінки або інша інформація -->
                <div>
                    <!-- Тут можна додати заголовок сторінки -->
                </div>

                <!-- Профіль користувача -->
                <div class="flex items-center">
                    <div class="flex items-center ms-3">
                        <div>
                            <button type="button"
                                class="flex text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600"
                                aria-expanded="false" data-dropdown-toggle="dropdown-user">
                                <span class="sr-only">Відкрити меню користувача</span>
                                <img class="w-8 h-8 rounded-full"
                                    src="https://flowbite.com/docs/images/people/profile-picture-5.jpg"
                                    alt="user photo">
                            </button>
                        </div>
                        <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded-sm shadow-sm dark:bg-gray-700 dark:divide-gray-600"
                            id="dropdown-user">

                            <div class="px-4 py-3">
                                <span
                                    class="block text-sm text-gray-900 dark:text-white">{{ Auth::user()->name }}</span>
                                <span
                                    class="text-sm font-medium text-gray-900 truncate dark:text-gray-300">{{ Auth::user()->email }}</span>
                            </div>
                            <ul class="py-1" role="none">

                                <li>
                                    <a href="{{ route('profile') }}"
                                        @if (class_exists('\Livewire\Features\SupportNavigateAttribute\Navigatable')) wire:navigate @endif
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white">
                                        {{ __('Profile') }}
                                    </a>
                                </li>
                                <!-- Authentication -->
                                <li>
                                    <button wire:click="logout" class="w-full text-start">
                                        <span
                                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white">
                                            {{ __('Log Out') }}
                                        </span>
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Додаємо script для роботи з подіями -->
        <script>
            document.addEventListener('livewire:initialized', () => {
                // Реєструємо слухача для події sidebar-toggle
                Livewire.on('sidebar-toggle', (data) => {
                    // При зміні стану сайдбару, передаємо подію у вікно
                    window.dispatchEvent(new CustomEvent('sidebar-state-updated', {
                        detail: {
                            collapsed: data.collapsed
                        }
                    }));
                });
            });
        </script>
    </div>
</div>
