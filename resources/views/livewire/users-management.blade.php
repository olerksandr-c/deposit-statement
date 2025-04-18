<div>
    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Таблиця користувачів') }}</h3>

    {{-- <div class="relative overflow-x-auto shadow-md sm:rounded-lg shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400"> --}}
    <div class="relative  shadow-md sm:rounded-lg">
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
                            <!-- Збільшено space-x-2 до space-x-3 -->
                            <!-- Кнопка редагування ролей -->
                            <div class="relative inline-flex group">
                                <button wire:click="editUserRoles({{ $user->id }})" type="button"
                                    class="p-3 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                    aria-label="{{ __('Редагувати ролі') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="h-6 w-6 text-indigo-600 dark:text-indigo-400" fill="none"
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

                            <!-- Кнопка перегляду дозволів -->
                            <div class="relative inline-flex group">
                                <button wire:click="viewUserEffectivePermissions({{ $user->id }})" type="button"
                                    class="p-3 rounded-lg hover:bg-green-100 dark:hover:bg-green-900 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-green-500"
                                    aria-label="{{ __('Переглянути дозволи') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="h-6 w-6 text-green-600 dark:text-green-400" fill="none"
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

    <!-- Модальне вікно редагування ролей -->
    @if ($showUserRolesModal && $selectedUser)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                {{-- Фон оверлея --}}
                <div wire:click="closeUserRolesModal()"
                    class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>

                {{-- Цей елемент для центрування модального контенту --}}
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                {{-- Модальна панель --}}
                <div
                    class="relative inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl dark:bg-gray-800 sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div>
                        <div class="mt-3 text-center sm:mt-0 sm:text-left">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100" id="modal-title">
                                {{ __('Редагувати ролі для') }} {{ $selectedUser->name }}
                            </h3>
                            <div class="mt-4">
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                                    {{ __('Виберіть ролі, які потрібно призначити користувачу.') }}
                                </p>
                                <fieldset>
                                    <legend class="sr-only">{{ __('Ролі') }}</legend>
                                    <div class="space-y-2">
                                        {{-- Перебираємо всі доступні ролі --}}
                                        @foreach ($allRoles as $role)
                                            <div class="relative flex items-start">
                                                <div class="flex items-center h-5">
                                                    {{-- Чекбокс для кожної ролі --}}
                                                    <input id="role-{{ $role->id }}" wire:model.live="userRoles"
                                                        {{-- Прив'язуємо до масиву userRoles --}} value="{{ $role->id }}"
                                                        {{-- Значення - ID ролі --}} type="checkbox"
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
                                {{-- Показ помилок валідації, якщо вони будуть --}}
                                {{-- @error('userRoles') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror --}}
                            </div>
                        </div>
                    </div>
                    {{-- Кнопки дій модального вікна --}}
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
                    {{-- Відображення повідомлення про успіх --}}
                    @if (session()->has('message'))
                        <div class="mt-4 text-sm text-green-600 dark:text-green-400">
                            {{ session('message') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Модальне вікно ефективних дозволів -->
    @if ($showUserEffectivePermissionsModal && $selectedUser)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                {{-- Фон оверлея --}}
                <div wire:click="closeUserEffectivePermissionsModal()"
                    class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>

                {{-- Цей елемент для центрування модального контенту --}}
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                {{-- Модальна панель --}}
                <div
                    class="relative inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl dark:bg-gray-800 sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div>
                        <div class="mt-3 text-center sm:mt-0 sm:text-left">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100"
                                id="modal-title">
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
                    {{-- Кнопки дій модального вікна --}}
                    <div class="mt-5 sm:mt-6">
                        <button wire:click="closeUserEffectivePermissionsModal()" type="button"
                            class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-sm">
                            {{ __('Закрити') }}
                        </button>
                    </div>
                    {{-- Відображення повідомлення про успіх --}}
                    @if (session()->has('message'))
                        <div class="mt-4 text-sm text-green-600 dark:text-green-400">
                            {{ session('message') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
