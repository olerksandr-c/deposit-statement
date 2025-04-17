 {{-- Сайдбар --}}
 <aside id="app-sidebar" x-cloak
     class="fixed inset-y-0 left-0  flex-shrink-0  bg-white dark:bg-gray-800 transition-all duration-100 ease-in-out "
     :class="sidebarOpen ? 'w-64' : 'w-20'" x-transition:enter="transform transition-transform duration-100 ease-in-out"
     x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
     x-transition:leave="transform transition-transform duration-100 ease-in-out" x-transition:leave-start="translate-x-0"
     x-transition:leave-end="-translate-x-full" x-show="sidebarOpen || !isMobile">

     <div class="flex flex-col h-full overflow-x-hidde"> {{-- <- overflow-x-hidden здесь может быть проблемой, но z-index должен помочь --}}
         {{-- Логотип и название ... --}}
         <div
             class="flex items-center h-16 px-4 border-r border-gray-200 dark:border-gray-700 transition-[width] duration-100 ease-in-out">
             {{-- Содержимое логотипа ... --}}
             <a href="/bank" class="flex items-center">
                 <svg width="32" height="32" viewBox="0 0 32 32" fill="#3B82F6"
                     xmlns="http://www.w3.org/2000/svg">
                     <rect x="4" y="4" width="24" height="24" rx="6" fill="inherit" />
                     <path
                         d="M16 10V22M19 13C19 11.8954 18.1046 11 17 11H15C13.8954 11 13 11.8954 13 13C13 14.1046 13.8954 15 15 15H17C18.1046 15 19 16.1046 19 17C19 18.1046 18.1046 19 17 19H15C13.8954 19 13 19.8954 13 21"
                         stroke="white" stroke-width="2" />
                 </svg>

                 <span x-show="sidebarOpen" class="ml-2 text-xl font-semibold dark:text-white whitespace-nowrap">
                     {{ config('app.name', 'Laravel') }}
                 </span>
             </a>
         </div>


         {{-- Навигация --}}
         <nav class="flex-1 px-4 py-4 space-y-2">
             {{-- Пример измененной ссылки --}}
             <a href="{{ route('bank') }}"
                 class="relative flex items-center px-2 py-2 text-sm font-medium rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 group"
                 x-data="{ showTooltip: false }" @mouseenter="!sidebarOpen && (showTooltip = true)"
                 @mouseleave="showTooltip = false">

                 <svg class="w-6 h-6 mr-3 flex-shrink-0 text-gray-800 dark:text-white" aria-hidden="true"
                     xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                     viewBox="0 0 24 24">
                     <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                         d="M19 10V4a1 1 0 0 0-1-1H9.914a1 1 0 0 0-.707.293L5.293 7.207A1 1 0 0 0 5 7.914V20a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2M10 3v4a1 1 0 0 1-1 1H5m5 6h9m0 0-2-2m2 2-2 2" />
                 </svg>

                 <span x-show="sidebarOpen" class="transition-opacity duration-200 whitespace-nowrap"
                     :class="{ 'opacity-0': !sidebarOpen }">Депозитна виписка</span>

                 <div x-show="showTooltip && !sidebarOpen" x-cloak x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                     class="ml-4 px-2 py-1 bg-gray-700 dark:bg-gray-900 text-white text-xs rounded shadow-lg whitespace-nowrap z-99">
                     Депозитна виписка
                 </div>
             </a>




             @role('administrator')
                 <a href="{{ route('role-permission-manager') }}"
                     class="flex items-center px-2 py-2 text-sm font-medium rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 group"
                     x-data="{ showTooltip: false }" @mouseenter="!sidebarOpen && (showTooltip = true)"
                     @mouseleave="showTooltip = false">

                     <svg class="w-6 h-6 mr-3 flex-shrink-0 text-gray-800 dark:text-white" aria-hidden="true"
                         xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                         viewBox="0 0 24 24">
                         <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                             d="M4.5 17H4a1 1 0 0 1-1-1 3 3 0 0 1 3-3h1m0-3.05A2.5 2.5 0 1 1 9 5.5M19.5 17h.5a1 1 0 0 0 1-1 3 3 0 0 0-3-3h-1m0-3.05a2.5 2.5 0 1 0-2-4.45m.5 13.5h-7a1 1 0 0 1-1-1 3 3 0 0 1 3-3h3a3 3 0 0 1 3 3 1 1 0 0 1-1 1Zm-1-9.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Z" />
                     </svg>
                     <span x-show="sidebarOpen" class="transition-opacity duration-200 whitespace-nowrap"
                         :class="{ 'opacity-0': !sidebarOpen }">Ролі
                         та права</span>
                     <div x-show="showTooltip && !sidebarOpen" x-cloak x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                         class="ml-4 px-2 py-1 bg-gray-700 dark:bg-gray-900 text-white text-xs rounded shadow-lg whitespace-nowrap z-99">
                         Ролі та права
                     </div>
                 </a>

                 <a href="{{ route('logs') }}"
                     class="flex items-center px-2 py-2 text-sm font-medium rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 group"
                     x-data="{ showTooltip: false }" @mouseenter="!sidebarOpen && (showTooltip = true)"
                     @mouseleave="showTooltip = false">
                     <svg class="w-6 h-6 mr-3 flex-shrink-0 text-gray-800 dark:text-white" aria-hidden="true"
                         xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                         viewBox="0 0 24 24">
                         <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                             d="m8 9 3 3-3 3m5 0h3M4 19h16a1 1 0 0 0 1-1V6a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1Z" />
                     </svg>
                     <span x-show="sidebarOpen" class="transition-opacity duration-200 whitespace-nowrap"
                         :class="{ 'opacity-0': !sidebarOpen }">Логування</span>
                     {{-- Якщо треба бейдж Admin --}}
                     {{-- <span
                 class="inline-flex items-center justify-center px-2 ms-3 text-sm font-medium text-gray-800 bg-gray-100 rounded-full dark:bg-gray-700 dark:text-gray-300"
                 :class="{ 'hidden': !$store.sidebar.isOpen }">Admin</span> --}}
                     <div x-show="showTooltip && !sidebarOpen" x-cloak x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                         class="ml-4 px-2 py-1 bg-gray-700 dark:bg-gray-900 text-white text-xs rounded shadow-lg whitespace-nowrap z-99">
                         Логування
                     </div>
                 </a>
             @endrole
             <a href="/docs/manual.pdf"
                 class="flex items-center px-2 py-2 text-sm font-medium rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 group"
                 x-data="{ showTooltip: false }" @mouseenter="!sidebarOpen && (showTooltip = true)"
                 @mouseleave="showTooltip = false">
                 <svg class="w-6 h-6 mr-3 flex-shrink-0 text-gray-800 dark:text-white" aria-hidden="true"
                     xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                     viewBox="0 0 24 24">
                     <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                         d="M9.529 9.988a2.502 2.502 0 1 1 5 .191A2.441 2.441 0 0 1 12 12.582V14m-.01 3.008H12M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                 </svg>
                 <span x-show="sidebarOpen" class="transition-opacity duration-200 whitespace-nowrap"
                     :class="{ 'opacity-0': !sidebarOpen }">Інструкція</span>
                 <div x-show="showTooltip && !sidebarOpen" x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                     class="ml-4 px-2 py-1 bg-gray-700 dark:bg-gray-900 text-white text-xs rounded shadow-lg whitespace-nowrap z-99">
                     Інструкція
                 </div>
             </a>

         </nav>
     </div>
 </aside>
