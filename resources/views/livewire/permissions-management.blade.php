<div>
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Таблиця дозволів') }}</h3>


       <div class="relative inline-flex group ml-auto">
            <div class="absolute right-full top-1/2 transform -translate-y-1/2 mr-2 z-10 invisible opacity-0 group-hover:visible group-hover:opacity-100 transition-all duration-200 px-3 py-1 text-sm font-medium text-white bg-gray-900 rounded-md shadow-sm whitespace-nowrap">
                {{ __('Створити дозвіл') }}
                <div class="absolute top-1/2 left-full -translate-y-1/2 w-0 h-0 border-t-4 border-b-4 border-l-0 border-r-4 border-solid border-gray-900 border-t-transparent border-b-transparent"></div>
            </div>
            <button wire:click="createPermission"  type="button"
                class="flex items-center justify-center w-12 h-12 rounded-full bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-700 dark:hover:bg-indigo-600 text-white transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                aria-label="{{ __('Створити дозвіл') }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
            </button>
        </div>
    </div>

    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">{{ __('Назва') }}</th>
                    <th scope="col" class="px-6 py-3">{{ __('Дії') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($permissions as $permission)
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                            {{ $permission->name }}
                        </td>
                        {{-- <td class="px-6 py-4 space-x-2">
                            <button wire:click="editPermission({{ $permission->id }})" type="button"
                                class="font-medium text-indigo-600 dark:text-indigo-500 hover:underline">
                                {{ __('Редагувати') }}
                            </button>
                            <button wire:click="deletePermission({{ $permission->id }})" type="button"
                                class="font-medium text-red-600 dark:text-red-500 hover:underline">
                                {{ __('Видалити') }}
                            </button>
                        </td> --}}

                        <td class="px-6 py-4 text-right space-x-3 whitespace-nowrap">
                            <!-- Кнопка редагування (карандаш) -->
                            <div class="relative inline-flex group">
                                <button wire:click="editPermission({{ $permission->id }})" type="button"
                                    class="p-3 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                    aria-label="{{ __('Редагувати') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                         class="h-6 w-6 text-indigo-600 dark:text-indigo-400"
                                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <div class="absolute z-10 invisible opacity-0 group-hover:visible group-hover:opacity-100 transition-all duration-200 bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-1 text-sm font-medium text-white bg-gray-900 rounded-md shadow-sm whitespace-nowrap">
                                    {{ __('Редагувати') }}
                                    <div class="absolute top-full left-1/2 -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-b-0 border-t-4 border-solid border-gray-900 border-l-transparent border-r-transparent"></div>
                                </div>
                            </div>



                            <!-- Кнопка видалення (кошик) -->
                            <div class="relative inline-flex group">
                                <button wire:click="deletePermission({{ $permission->id }})" type="button"
                                    class="p-3 rounded-lg hover:bg-red-100 dark:hover:bg-red-900 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-red-500"
                                    aria-label="{{ __('Видалити') }}"
                                    onclick="confirm('{{ __('Ви впевнені?') }}') || event.stopImmediatePropagation()">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                         class="h-6 w-6 text-red-600 dark:text-red-400"
                                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                                <div class="absolute z-10 invisible opacity-0 group-hover:visible group-hover:opacity-100 transition-all duration-200 bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-1 text-sm font-medium text-white bg-gray-900 rounded-md shadow-sm whitespace-nowrap">
                                    {{ __('Видалити') }}
                                    <div class="absolute top-full left-1/2 -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-b-0 border-t-4 border-solid border-gray-900 border-l-transparent border-r-transparent"></div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                        <td colspan="2" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                            {{ __('Дозволів не знайдено.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Модальне вікно створення/редагування дозволу -->
    @if ($showPermissionModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div wire:click="closePermissionModal" class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                            {{ $selectedPermission ? 'Редагувати дозвіл' : 'Створити новий дозвіл' }}
                        </h3>
                        <input wire:model="permissionName" type="text"
                            class="w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            placeholder="Назва дозволу (наприклад, edit-posts)">
                        @error('permissionName') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button wire:click="savePermission" type="button"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                            {{ __('Зберегти') }}
                        </button>
                        <button wire:click="closePermissionModal" type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm dark:bg-gray-600 dark:text-gray-200 dark:border-gray-600">
                            {{ __('Скасувати') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
