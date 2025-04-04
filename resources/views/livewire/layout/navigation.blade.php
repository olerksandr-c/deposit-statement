<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;


new class extends Component {
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>





<nav class="bg-white border-gray-200 dark:bg-gray-900">
    <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
        <a href="{{ config('app.url') }}" class="flex items-center space-x-3 rtl:space-x-reverse">
            <svg width="32" height="32" viewBox="0 0 32 32" fill="#3B82F6" xmlns="http://www.w3.org/2000/svg">
                <rect x="4" y="4" width="24" height="24" rx="6" fill="inherit" />
                <path
                    d="M16 10V22M19 13C19 11.8954 18.1046 11 17 11H15C13.8954 11 13 11.8954 13 13C13 14.1046 13.8954 15 15 15H17C18.1046 15 19 16.1046 19 17C19 18.1046 18.1046 19 17 19H15C13.8954 19 13 19.8954 13 21"
                    stroke="white" stroke-width="2" />
            </svg>
            <span class="self-center text-2xl font-semibold whitespace-nowrap dark:text-white">Депозитна виписка</span>
        </a>

        <div class="flex items-center md:order-2 space-x-3 md:space-x-0 rtl:space-x-reverse">
            <button type="button"
                class="flex text-sm bg-gray-200 dark:bg-gray-100 rounded-full md:me-0 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-100"
                id="user-menu-button" aria-expanded="false" data-dropdown-toggle="user-dropdown"
                data-dropdown-placement="bottom">
                <span class="sr-only">Open user menu</span>
                <svg class="w-6 h-6 text-blue-600" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"
                    xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd"
                        d="M12 4a4 4 0 1 0 0 8 4 4 0 0 0 0-8Zm-2 9a4 4 0 0 0-4 4v1a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2v-1a4 4 0 0 0-4-4h-4Z"
                        clip-rule="evenodd" />
                </svg>


            </button>
            <!-- Dropdown menu -->
            <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded-lg shadow-sm dark:bg-gray-700 dark:divide-gray-600"
                id="user-dropdown">
                <div class="px-4 py-3">
                    <span class="block text-sm text-gray-900 dark:text-white">{{ Auth::user()->name }}</span>
                    <span
                        class="block text-sm text-gray-500 truncate dark:text-gray-400">{{ Auth::user()->email }}</span>
                </div>
                <ul class="py-2 text-gray-900 dark:text-white" aria-labelledby="user-menu-button">
                    <!-- Profile -->
                    <li>
                        <a href="{{ route('profile') }}" @if (class_exists('\Livewire\Features\SupportNavigateAttribute\Navigatable')) wire:navigate @endif
                            class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
                            {{ __('Profile') }}
                        </a>
                    </li>
                    <!-- Authentication -->
                    <li>
                        <button wire:click="logout" class="w-full text-start">
                            <span
                                class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
                                {{ __('Log Out') }}
                            </span>
                        </button>
                    </li>
                </ul>
            </div>
            <button data-collapse-toggle="navbar-user" type="button"
                class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600"
                aria-controls="navbar-user" aria-expanded="false">
                <span class="sr-only">Open main menu</span>
                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 17 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M1 1h15M1 7h15M1 13h15" />
                </svg>
            </button>
        </div>
        <div class="items-center justify-between hidden w-full md:flex md:w-auto md:order-1" id="navbar-user">
            <ul
                class="flex flex-col font-medium p-4 md:p-0 mt-4 border border-gray-100 rounded-lg bg-gray-50 md:space-x-8 rtl:space-x-reverse md:flex-row md:mt-0 md:border-0 md:bg-white dark:bg-gray-800 md:dark:bg-gray-900 dark:border-gray-700">
                <li>
                    <a href="{{ route('bank') }}"
                        class="{{ request()->routeIs('bank') ? 'block py-2 px-3 text-white bg-blue-700 rounded-sm md:bg-transparent md:text-blue-700 md:p-0 md:dark:text-blue-500' : 'block py-2 px-3 text-gray-900 rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 dark:text-white md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent' }}"
                        @if (class_exists('\Livewire\Features\SupportNavigateAttribute\Navigatable')) wire:navigate @endif
                        aria-current="{{ request()->routeIs('bank') ? 'page' : 'false' }}">
                        Депозитна виписка
                    </a>
                </li>

                <li>
                    <a href="{{ route('logs') }}"
                        class="{{ request()->routeIs('logs') ? 'block py-2 px-3 text-white bg-blue-700 rounded-sm md:bg-transparent md:text-blue-700 md:p-0 md:dark:text-blue-500' : 'block py-2 px-3 text-gray-900 rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 dark:text-white md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent' }}"
                        @if (class_exists('\Livewire\Features\SupportNavigateAttribute\Navigatable')) wire:navigate @endif
                        aria-current="{{ request()->routeIs('logs') ? 'page' : 'false' }}">
                        Логування
                </li>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link href="/docs/manual.pdf" target="_blank"
                        class="text-gray-800 dark:text-gray-200 hover:text-gray-900 dark:hover:text-white">
                        Інструкція
                    </x-nav-link>
                </div>



            </ul>
        </div>
    </div>
</nav>
