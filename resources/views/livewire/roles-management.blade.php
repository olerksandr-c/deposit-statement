<div>
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Таблиця ролей') }}</h3>
        <button wire:click="openRoleCreateModal" type="button"
            class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
            {{ __('Створити роль') }}
        </button>
    </div>

    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <!-- Заголовки таблиці (як раніше) -->
            <tbody>
                @forelse ($roles as $role)
                    <tr
                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                            {{ $role->name }}
                        </td>
                        <td class="px-6 py-4">
                            <!-- Дозволи ролі (як раніше) -->
                        </td>
                        <td class="px-6 py-4 space-x-2">
                            <button wire:click="editRolePermissions({{ $role->id }})" type="button"
                                class="font-medium text-indigo-600 dark:text-indigo-500 hover:underline">
                                {{ __('Редагувати дозволи') }}
                            </button>
                            <button wire:click="deleteRole({{ $role->id }})" type="button"
                                class="font-medium text-red-600 dark:text-red-500 hover:underline"
                                onclick="confirm('Ви впевнені?') || event.stopImmediatePropagation()">
                                {{ __('Видалити') }}
                            </button>
                        </td>
                    </tr>
                @empty
                    <!-- Пустий стан (як раніше) -->
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Модальне вікно створення ролі -->
    @if ($showRoleCreateModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div wire:click="closeRoleCreateModal"
                    class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                            {{ __('Створити нову роль') }}
                        </h3>
                        <input wire:model="newRoleName" type="text"
                            class="w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            placeholder="Назва ролі (наприклад, editor)">
                        @error('newRoleName')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button wire:click="createRole" type="button"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                            {{ __('Зберегти') }}
                        </button>
                        <button wire:click="closeRoleCreateModal" type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm dark:bg-gray-600 dark:text-gray-200 dark:border-gray-600">
                            {{ __('Скасувати') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Модальне вікно редагування дозволів (як раніше) -->
    @if ($showRolePermissionsModal && $selectedRole)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                {{-- Фон оверлея --}}
                <div wire:click="closeRolePermissionsModal()"
                    class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>

                {{-- Цей елемент для центрування модального контенту --}}
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                {{-- Модальна панель --}}
                <div
                    class="relative inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl dark:bg-gray-800 sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div>
                        <div class="mt-3 text-center sm:mt-0 sm:text-left">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100" id="modal-title">
                                {{ __('Редагувати дозволи для ролі') }} {{ $selectedRole->name }}
                            </h3>
                            <div class="mt-4">
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                                    {{ __('Виберіть дозволи, які потрібно призначити цій ролі.') }}
                                </p>
                                <fieldset>
                                    <legend class="sr-only">{{ __('Дозволи') }}</legend>
                                    <div class="space-y-2">
                                        {{-- Перебираємо всі доступні ролі --}}
                                        @foreach ($permissions as $permission)
                                            <div class="relative flex items-start">
                                                <div class="flex items-center h-5">
                                                    {{-- Чекбокс для кожного дозволу --}}
                                                    <input id="permission-{{ $permission->id }}"
                                                        wire:model.live="rolePermissions" {{-- Прив'язка до $rolePermissions --}}
                                                        value="{{ $permission->id }}" {{-- Значення - ID дозволу --}}
                                                        type="checkbox"
                                                        class="w-4 h-4 text-indigo-600 border-gray-300 rounded dark:border-gray-600 dark:bg-gray-700 focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:ring-offset-gray-800">
                                                </div>
                                                <div class="ml-3 text-sm">
                                                    <label for="permission-{{ $permission->id }}"
                                                        class="font-medium text-gray-700 dark:text-gray-300">{{ $permission->name }}</label>
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
                        <button wire:click="updateRolePermissions()" type="button"
                            class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:col-start-2 sm:text-sm">
                            {{ __('Зберегти') }}
                        </button>
                        <button wire:click="closeRolePermissionsModal()" type="button"
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
</div>
