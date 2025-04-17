@section('title', 'Логування')

<div class="container mt-5 px-5 py-5 mb-5 mx-auto w-full sm:rounded-lg">
    <div class="flex flex-column sm:flex-row flex-wrap space-y-4 sm:space-y-0 items-center justify-between pb-4">
        <!-- Фільтр по датам -->

        <div class="relative">
            <!-- Кнопка для открытия dropdown -->
            <button
                class="inline-flex items-center text-gray-500 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-3 py-1.5 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700 peer"
                type="button"
            >
                <svg class="w-3 h-3 text-gray-500 dark:text-gray-400 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 0a10 10 0 1 0 10 10A10.011 10.011 0 0 0 10 0Zm3.982 13.982a1 1 0 0 1-1.414 0l-3.274-3.274A1.012 1.012 0 0 1 9 10V6a1 1 0 0 1 2 0v3.586l2.982 2.982a1 1 0 0 1 0 1.414Z"/>
                </svg>
                @if($dateFilter == 'all')
                    За все время
                @elseif($dateFilter == 'day')
                    За последний день
                @elseif($dateFilter == 'week')
                    За последние 7 дней
                @elseif($dateFilter == 'month')
                    За последние 30 дней
                @elseif($dateFilter == 'year')
                    За последний год
                @endif
                <svg class="w-2.5 h-2.5 ms-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                </svg>
            </button>

            <!-- Dropdown menu -->
            <div class="absolute z-10 hidden w-48 bg-white divide-y divide-gray-100 rounded-lg shadow-sm dark:bg-gray-700 dark:divide-gray-600 peer-focus:block hover:block">
                <ul class="p-3 space-y-1 text-sm text-gray-700 dark:text-gray-200">
                    <li>
                        <div class="flex items-center p-2 rounded-sm hover:bg-gray-100 dark:hover:bg-gray-600">
                            <input id="filter-radio-all" type="radio" value="all" wire:model.live="dateFilter" name="filter-radio" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                            <label for="filter-radio-all" class="w-full ms-2 text-sm font-medium text-gray-900 rounded-sm dark:text-gray-300">За все время</label>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center p-2 rounded-sm hover:bg-gray-100 dark:hover:bg-gray-600">
                            <input id="filter-radio-day" type="radio" value="day" wire:model.live="dateFilter" name="filter-radio" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                            <label for="filter-radio-day" class="w-full ms-2 text-sm font-medium text-gray-900 rounded-sm dark:text-gray-300">За последний день</label>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center p-2 rounded-sm hover:bg-gray-100 dark:hover:bg-gray-600">
                            <input id="filter-radio-week" type="radio" value="week" wire:model.live="dateFilter" name="filter-radio" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                            <label for="filter-radio-week" class="w-full ms-2 text-sm font-medium text-gray-900 rounded-sm dark:text-gray-300">За последние 7 дней</label>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center p-2 rounded-sm hover:bg-gray-100 dark:hover:bg-gray-600">
                            <input id="filter-radio-month" type="radio" value="month" wire:model.live="dateFilter" name="filter-radio" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                            <label for="filter-radio-month" class="w-full ms-2 text-sm font-medium text-gray-900 rounded-sm dark:text-gray-300">За последние 30 дней</label>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center p-2 rounded-sm hover:bg-gray-100 dark:hover:bg-gray-600">
                            <input id="filter-radio-year" type="radio" value="year" wire:model.live="dateFilter" name="filter-radio" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                            <label for="filter-radio-year" class="w-full ms-2 text-sm font-medium text-gray-900 rounded-sm dark:text-gray-300">За последний год</label>
                        </div>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Пошук за іменем користувача -->
        <div class="relative">
            <div class="absolute inset-y-0 left-0 rtl:inset-r-0 rtl:right-0 flex items-center ps-3 pointer-events-none">
                <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <input type="text" wire:model.live="userName" class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Поиск по имени пользователя">
        </div>

        <!-- Кнопка скидання фільтрів (показується тільки коли є активні фільтри) -->
        @if($userName || $dateFilter !== 'all')
        <button wire:click="$set('userName', ''); $set('dateFilter', 'all');" class="px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700">
            Сбросить все фильтры
        </button>
        @endif
    </div>

    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3">
                    Дата и время
                </th>
                <th scope="col" class="px-6 py-3">
                    Имя пользователя
                </th>
                <th scope="col" class="px-6 py-3">
                    Тип
                </th>
                <th scope="col" class="px-6 py-3">
                    Сообщение
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($logs as $log)
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                    <td class="px-6 py-4">
                        {{ $log->created_at->format('d.m.Y H:i:s') }}
                    </td>
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        {{ $log->user->name ?? 'Неизвестный' }}
                    </th>
                    <td class="px-6 py-4">
                        {{ $log->log_type }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $log->message }}
                    </td>
                </tr>
            @endforeach

            @if($logs->count() == 0)
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                    <td colspan="4" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                        Записи не найдены
                    </td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="mt-4 px-4">
        {{ $logs->links() }}
    </div>
</div>
