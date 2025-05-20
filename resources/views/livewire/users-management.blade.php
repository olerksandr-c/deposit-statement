<div>
    {{-- Заголовок та кнопка додавання користувача --}}
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Таблиця користувачів') }}</h3>
        <button wire:click="openLdapSearchModal" type="button"
            class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-500 dark:hover:bg-blue-600">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd"
                    d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                    clip-rule="evenodd" />
            </svg>
            {{ __('Додати з LDAP') }}
        </button>
    </div>

    {{-- Повідомлення сесії для основної сторінки --}}
    @if (session()->has('message'))
    <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-200 dark:text-green-800" role="alert">
        {{ session('message') }}
    </div>
    @endif
    @if (session()->has('error'))
    <div class="mb-4 p-4 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-red-200 dark:text-red-800" role="alert">
        {{ session('error') }}
    </div>
    @endif


    <div class="relative shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">{{ __('Ім\'я користувача') }}</th>
                    <th scope="col" class="px-6 py-3">{{ __('Email') }}</th>
                    <th scope="col" class="px-6 py-3">{{ __('Ролі') }}</th>
                    <th scope="col" class="px-6 py-3 text-right">{{ __('Дії') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                <tr
                    class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                    <th scope="row"
                        class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        {{ $user->name }}
                    </th>
                    <td class="px-6 py-4 truncate max-w-[120px] md:max-w-none">
                        {{ $user->email }}
                    </td>
                    <td class="px-6 py-4">
                        @if ($user->roles->isNotEmpty())
                        <div class="flex flex-wrap gap-1">
                            @foreach ($user->roles as $role)
                            <span
                                class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">
                                {{ $role->name }}
                            </span>
                            @endforeach
                        </div>
                        @else
                        <span
                            class="text-xs italic text-gray-400 dark:text-gray-500">{{ __('Немає ролей') }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right space-x-3 whitespace-nowrap">
                        <div class="relative inline-flex group">
                            <button wire:click="editUserRoles({{ $user->id }})" type="button"
                                class="p-2 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                aria-label="{{ __('Редагувати ролі') }}">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5 text-indigo-600 dark:text-indigo-400" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
                            <div
                                class="absolute z-10 invisible opacity-0 group-hover:visible group-hover:opacity-100 transition-all duration-200 bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-1 text-sm font-medium text-white bg-gray-900 rounded-md shadow-sm whitespace-nowrap">
                                {{ __('Редагувати ролі') }}
                                <div
                                    class="absolute top-full left-1/2 -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-b-0 border-t-4 border-solid border-gray-900 border-l-transparent border-r-transparent">
                                </div>
                            </div>
                        </div>

                        <div class="relative inline-flex group">
                            <button wire:click="viewUserEffectivePermissions({{ $user->id }})" type="button"
                                class="p-2 rounded-lg hover:bg-green-100 dark:hover:bg-green-900 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-green-500"
                                aria-label="{{ __('Переглянути дозволи') }}">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5 text-green-600 dark:text-green-400" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                            <div
                                class="absolute z-10 invisible opacity-0 group-hover:visible group-hover:opacity-100 transition-all duration-200 bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-1 text-sm font-medium text-white bg-gray-900 rounded-md shadow-sm whitespace-nowrap">
                                {{ __('Переглянути дозволи') }}
                                <div
                                    class="absolute top-full left-1/2 -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-b-0 border-t-4 border-solid border-gray-900 border-l-transparent border-r-transparent">
                                </div>
                            </div>
                        </div>

                        <!-- Видалення користувача -->
                        <div class="relative inline-flex group">
                            <button wire:click="$dispatch('openDeleteConfirmation', { userId: {{ $user->id }} })"
                                type="button"
                                class="p-2 rounded-lg hover:bg-red-100 dark:hover:bg-red-900 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-red-500"
                                aria-label="{{ __('Видалити користувача') }}">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5 text-red-600 dark:text-red-400" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                            <div
                                class="absolute z-10 invisible opacity-0 group-hover:visible group-hover:opacity-100 transition-all duration-200 bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-1 text-sm font-medium text-white bg-gray-900 rounded-md shadow-sm whitespace-nowrap">
                                {{ __('Видалити') }}
                                <div
                                    class="absolute top-full left-1/2 -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-b-0 border-t-4 border-solid border-gray-900 border-l-transparent border-r-transparent">
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                    <td colspan="4" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                        {{ __('Користувачів не знайдено.') }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Модальне вікно редагування ролей (ваш існуючий код) --}}
    @if ($showUserRolesModal && $selectedUser)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="user-roles-modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div wire:click="closeUserRolesModal()"
                class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75 dark:bg-opacity-80" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="relative inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl dark:bg-gray-800 sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div>
                    <div class="mt-3 text-center sm:mt-0 sm:text-left">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100" id="user-roles-modal-title">
                            {{ __('Редагувати ролі для') }} {{ $selectedUser->name }}
                        </h3>
                        <div class="mt-4">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                                {{ __('Виберіть ролі, які потрібно призначити користувачу.') }}
                            </p>
                            <fieldset>
                                <legend class="sr-only">{{ __('Ролі') }}</legend>
                                <div class="space-y-2">
                                    @foreach ($allRoles as $role)
                                    <div class="relative flex items-start">
                                        <div class="flex items-center h-5">
                                            <input id="role-{{ $role->id }}" wire:model.live="userRoles"
                                                value="{{ $role->id }}" type="checkbox"
                                                class="w-4 h-4 text-indigo-600 border-gray-300 rounded dark:border-gray-600 dark:bg-gray-700 focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:ring-offset-gray-800">
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="role-{{ $role->id }}"
                                                class="font-medium text-gray-700 dark:text-gray-300">{{ $role->name }}</label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </fieldset>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                    <button wire:click="updateUserRoles()" type="button"
                        class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:col-start-2 sm:text-sm">
                        {{ __('Зберегти') }}
                    </button>
                    <button wire:click="closeUserRolesModal()" type="button"
                        class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                        {{ __('Скасувати') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Модальне вікно ефективних дозволів (ваш існуючий код) --}}
    @if ($showUserEffectivePermissionsModal && $selectedUser)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="user-permissions-modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div wire:click="closeUserEffectivePermissionsModal()"
                class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75 dark:bg-opacity-80" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="relative inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl dark:bg-gray-800 sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div>
                    <div class="mt-3 text-center sm:mt-0 sm:text-left">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100"
                            id="user-permissions-modal-title">
                            {{ __('Ефективні дозволи для') }} {{ $selectedUser->name }}
                        </h3>
                        <div class="mt-4">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                                {{ __('Цей користувач має наступні дозволи через свої ролі:') }}
                            </p>
                            @if ($effectivePermissions->isNotEmpty())
                            <div class="flex flex-wrap gap-1">
                                @foreach ($effectivePermissions as $permission)
                                <span
                                    class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">
                                    {{ $permission->name }}
                                </span>
                                @endforeach
                            </div>
                            @else
                            <p class="text-sm italic text-gray-400 dark:text-gray-500">
                                {{ __('Користувач не має жодних ефективних дозволів.') }}
                            </p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-6">
                    <button wire:click="closeUserEffectivePermissionsModal()" type="button"
                        class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-sm">
                        {{ __('Закрити') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif


    {{-- Модальне вікно пошуку та додавання користувачів з LDAP --}}
    @if ($showLdapSearchModal)
    <div class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="ldap-search-modal-title" role="dialog"
        aria-modal="true">
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            {{-- Фон оверлея --}}
            <div wire:click="closeLdapSearchModal()"
                class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75 dark:bg-opacity-80" aria-hidden="true">
            </div>

            {{-- Цей елемент для центрування модального контенту --}}
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            {{-- Модальна панель --}}
            <div
                class="relative inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl dark:bg-gray-800 sm:my-8 sm:align-middle sm:max-w-2xl md:max-w-3xl lg:max-w-4xl sm:w-full sm:p-6">
                <div>
                    <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100 mb-4"
                        id="ldap-search-modal-title">
                        {{ __('Пошук та додавання користувачів з LDAP') }}
                    </h3>

                    {{-- Повідомлення сесії для LDAP модалки --}}
                    @if (session()->has('ldap_message'))
                    <div class="mb-4 p-3 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-700 dark:text-green-100" role="alert">
                        {{ session('ldap_message') }}
                    </div>
                    @endif
                    @if (session()->has('ldap_error'))
                    <div class="mb-4 p-3 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-red-700 dark:text-red-100" role="alert">
                        {{ session('ldap_error') }}
                    </div>
                    @endif

                    {{-- Статус підключення до LDAP --}}
                    <div class="mb-4 p-3 rounded-md @if ($ldapIsConnected) bg-green-50 dark:bg-green-700 @else bg-red-50 dark:bg-red-700 @endif">
                        @if ($ldapIsConnected)
                        <p class="text-sm font-medium text-green-700 dark:text-green-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="inline-block h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            {{ __('Підключення до LDAP успішно встановлено.') }}
                        </p>
                        @else
                        <p class="text-sm font-medium text-red-700 dark:text-red-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="inline-block h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                            {{ __('Помилка підключення до LDAP:') }}
                            <span class="font-normal">{{ $ldapConnectionError ?: __('Не вдалося підключитися.') }}</span>
                        </p>
                        @endif
                    </div>

                    {{-- Форма пошуку LDAP --}}
                    @if ($ldapIsConnected)
                    <div class="mb-4">
                        <label for="ldapSearchQueryInput" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Пошуковий запит') }}</label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                            <input wire:model.live.debounce.500ms="ldapSearchQuery" wire:keydown.enter="searchLdapUsers"
                                id="ldapSearchQueryInput" type="text"
                                placeholder="{{ __('Ім\'я, логін, email...') }}"
                                class="flex-1 block w-full rounded-none rounded-l-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                @if($ldapLoading) disabled @endif>
                            <button wire:click="searchLdapUsers" type="button" @if($ldapLoading) disabled @endif
                                class="inline-flex items-center px-4 py-2 border border-l-0 border-gray-300 dark:border-gray-600 rounded-r-md bg-gray-50 dark:bg-gray-600 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 @if($ldapLoading) opacity-50 cursor-not-allowed @endif">
                                <svg wire:loading wire:target="searchLdapUsers" class="animate-spin -ml-1 mr-2 h-4 w-4 text-gray-700 dark:text-gray-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span wire:loading.remove wire:target="searchLdapUsers">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-700 dark:text-gray-200" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                                <span wire:loading wire:target="searchLdapUsers">{{ __('Пошук...') }}</span>
                            </button>
                        </div>
                    </div>

                    {{-- Результати пошуку LDAP --}}
                    <div wire:loading.class="opacity-50" wire:target="searchLdapUsers, importLdapUser" class="mt-2 max-h-96 overflow-y-auto">
                        @if (!empty($ldapSearchQuery) && !$ldapLoading && empty($ldapUsers))
                        <p class="text-sm text-gray-500 dark:text-gray-400 py-4 text-center">{{ __('Користувачів не знайдено за вашим запитом.') }}</p>
                        @elseif (!empty($ldapUsers))
                        <div class="align-middle inline-block min-w-full">
                            <div class="shadow overflow-hidden border-b border-gray-200 dark:border-gray-700 sm:rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Ім\'я (Display Name)') }}</th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Логін (sAMAccountName)') }}</th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Email') }}</th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Відділ') }}</th>
                                            <th scope="col" class="relative px-4 py-3"><span class="sr-only">{{ __('Додати') }}</span></th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach ($ldapUsers as $ldapUser)
                                        @php
                                        $guid = $ldapUser['objectguid'][0] ?? null;
                                        $displayName = $ldapUser['displayname'][0] ?? ($ldapUser['cn'][0] ?? __('N/A'));
                                        $samAccountName = $ldapUser['samaccountname'][0] ?? __('N/A');
                                        $mail = $ldapUser['mail'][0] ?? __('N/A');
                                        $department = $ldapUser['department'][0] ?? __('N/A');
                                        // Перевірка, чи користувач вже імпортований
                                        $isImported = $guid ? \App\Models\User::where('guid', $guid)->exists() : (\App\Models\User::where('email', $mail)->whereNotNull('email')->exists());
                                        @endphp
                                        <tr>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $displayName }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $samAccountName }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $mail }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300 truncate max-w-xs">{{ $department }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                                @if($guid)
                                                @if($isImported)
                                                <span class="text-xs italic text-green-600 dark:text-green-400">{{ __('Вже імпортовано') }}</span>
                                                @else
                                                <button wire:click="importLdapUser('{{ $guid }}')" wire:loading.attr="disabled" wire:target="importLdapUser('{{ $guid }}')"
                                                    type="button"
                                                    class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-indigo-500 dark:hover:bg-indigo-600 disabled:opacity-50">
                                                    {{ __('Додати') }}
                                                    <svg wire:loading wire:target="importLdapUser('{{ $guid }}')" class="animate-spin ml-1.5 h-3 w-3 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                    </svg>
                                                </button>
                                                @endif
                                                @else
                                                <span class="text-xs italic text-red-500 dark:text-red-400">{{ __('Відсутній GUID') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @elseif(empty($ldapSearchQuery) && !$ldapLoading)
                        <p class="text-sm text-gray-500 dark:text-gray-400 py-4 text-center">{{ __('Введіть запит для пошуку користувачів в LDAP.') }}</p>
                        @endif
                    </div>
                    @endif
                </div>

                {{-- Кнопки дій модального вікна LDAP --}}
                <div class="mt-5 sm:mt-6">
                    <button wire:click="closeLdapSearchModal()" type="button"
                        class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-sm">
                        {{ __('Скасувати') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
    {{-- Модальне вікно підтвердження видалення --}}
    @if ($showDeleteConfirmModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="delete-confirm-modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div wire:click="closeDeleteConfirmModal()" class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75 dark:bg-opacity-80" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="relative inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl dark:bg-gray-800 sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" id="modal-title">
                            {{ __('Підтвердження видалення') }}
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ __('Ви впевнені, що хочете видалити цього користувача? Цю дію не можна скасувати.') }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                    <button wire:click="deleteUser" type="button"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        {{ __('Видалити') }}
                    </button>
                    <button wire:click="closeDeleteConfirmModal" type="button"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-700 text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                        {{ __('Скасувати') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>