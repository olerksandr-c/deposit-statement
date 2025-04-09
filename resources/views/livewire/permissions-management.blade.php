<div>
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Таблиця дозволів') }}</h3>
        <button wire:click="createPermission" type="button"
            class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
            {{ __('Створити дозвіл') }}
        </button>
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
                        <td class="px-6 py-4 space-x-2">
                            <button wire:click="editPermission({{ $permission->id }})" type="button"
                                class="font-medium text-indigo-600 dark:text-indigo-500 hover:underline">
                                {{ __('Редагувати') }}
                            </button>
                            <button wire:click="deletePermission({{ $permission->id }})" type="button"
                                class="font-medium text-red-600 dark:text-red-500 hover:underline">
                                {{ __('Видалити') }}
                            </button>
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
