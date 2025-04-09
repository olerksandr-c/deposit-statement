<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Керування ролями та доступами') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{-- Навігація по вкладках --}}
                    <div class="mb-4 border-b border-gray-200 dark:border-gray-700">
                        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="myTab"
                            data-tabs-toggle="#myTabContent" role="tablist">
                            <li class="mr-2" role="presentation">
                                <button wire:click="switchTab('users')"
                                    class="inline-block p-4 border-b-2 rounded-t-lg {{ $activeTab === 'users' ? 'border-indigo-500 text-indigo-600 dark:border-indigo-400 dark:text-indigo-400' : 'border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300' }}"
                                    id="users-tab" type="button" role="tab" aria-controls="users"
                                    aria-selected="{{ $activeTab === 'users' ? 'true' : 'false' }}">
                                    {{ __('Користувачі') }}
                                </button>
                            </li>
                            <li class="mr-2" role="presentation">
                                <button wire:click="switchTab('roles')"
                                    class="inline-block p-4 border-b-2 rounded-t-lg {{ $activeTab === 'roles' ? 'border-indigo-500 text-indigo-600 dark:border-indigo-400 dark:text-indigo-400' : 'border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300' }}"
                                    id="roles-tab" type="button" role="tab" aria-controls="roles"
                                    aria-selected="{{ $activeTab === 'roles' ? 'true' : 'false' }}">
                                    {{ __('Ролі') }}
                                </button>
                            </li>
                            <li class="mr-2" role="presentation">
                                <button wire:click="switchTab('permissions')"
                                    class="inline-block p-4 border-b-2 rounded-t-lg {{ $activeTab === 'permissions' ? 'border-indigo-500 text-indigo-600 dark:border-indigo-400 dark:text-indigo-400' : 'border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300' }}"
                                    id="permissions-tab" type="button" role="tab" aria-controls="permissions"
                                    aria-selected="{{ $activeTab === 'permissions' ? 'true' : 'false' }}">
                                    {{ __('Дозволи') }}
                                </button>
                            </li>

                        </ul>
                    </div>

                    {{-- Контент вкладок --}}
                    <div id="myTabContent">
                        @if ($activeTab === 'users')
                            <livewire:users-management />
                        @elseif($activeTab === 'roles')
                            <livewire:roles-management />
                        @elseif($activeTab === 'permissions')
                            <livewire:permissions-management />
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
