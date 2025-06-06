<nav class="border-gray-200 bg-gray-100 dark:bg-gray-800 dark:border-gray-700">
    <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
        <a href="https://deposit-statement.energy.cn.ua/" class="flex items-center space-x-3 rtl:space-x-reverse">
            <img src="https://www.svgrepo.com/download/353985/laravel.svg" class="h-8" alt="Logo" />
            <span class="self-center text-2xl font-semibold whitespace-nowrap dark:text-white">Депозитна виписка</span>
        </a>

        <!-- Кнопка меню для мобильных -->
        <button data-collapse-toggle="navbar-default" type="button"
            class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600">
            <span class="sr-only">Open main menu</span>
            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h15M1 7h15M1 13h15"/>
            </svg>
        </button>

        <div class="hidden w-full md:block md:w-auto" id="navbar-default">
            <ul class="font-medium flex flex-col items-center md:flex-row md:space-x-6 rtl:space-x-reverse md:mt-0">
                <li class="flex items-center space-x-4">
                    <!-- Пункт меню "Інструкція" -->
                    <a class="block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 dark:text-white md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent"
                        href="{{ asset('docs/manual.pdf') }}" target="_blank">Інструкція
                    </a>

                    <!-- Переключатель темы -->
                    <button id="theme-toggle" type="button"
                        class="text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm p-2.5">
                        <!-- Иконка солнца (светлая тема) -->
                        <svg id="theme-toggle-light-icon" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path>
                        </svg>
                        <!-- Иконка луны (темная тема) -->
                        <svg id="theme-toggle-dark-icon" class="w-5 h-5 hidden" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                        </svg>
                    </button>
                </li>
            </ul>
        </div>
    </div>
</nav>

<script>
    // Определяем текущую тему из localStorage или системных настроек
    const userTheme = localStorage.getItem('theme');
    const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches;

    // Функция проверки темы
    const themeCheck = () => {
        if (userTheme === 'dark' || (!userTheme && systemTheme)) {
            document.documentElement.classList.add('dark');
            document.getElementById('theme-toggle-dark-icon').classList.add('hidden');
            document.getElementById('theme-toggle-light-icon').classList.remove('hidden');
            return;
        }
        document.getElementById('theme-toggle-light-icon').classList.add('hidden');
        document.getElementById('theme-toggle-dark-icon').classList.remove('hidden');
    };

    // Ручное переключение темы
    const themeSwitch = () => {
        if (document.documentElement.classList.contains('dark')) {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('theme', 'light');
            document.getElementById('theme-toggle-light-icon').classList.add('hidden');
            document.getElementById('theme-toggle-dark-icon').classList.remove('hidden');
        } else {
            document.documentElement.classList.add('dark');
            localStorage.setItem('theme', 'dark');
            document.getElementById('theme-toggle-dark-icon').classList.add('hidden');
            document.getElementById('theme-toggle-light-icon').classList.remove('hidden');
        }
    };

    // Вешаем обработчик на кнопку
    document.getElementById('theme-toggle').addEventListener('click', themeSwitch);

    // Проверяем тему при загрузке
    themeCheck();
</script>
