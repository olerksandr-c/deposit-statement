<div class="fixed inset-x-0 top-0 flex flex-col items-center justify-start pt-4 px-4 pointer-events-none z-50 space-y-2">
    @foreach($notifications as $notification)
        @php
            // Определяем цвета в зависимости от типа
            $bgColors = [
                'success' => 'bg-green-50 dark:bg-green-900/30',
                'error' => 'bg-red-50 dark:bg-red-900/30',
                'warning' => 'bg-yellow-50 dark:bg-yellow-900/30',
                'info' => 'bg-blue-50 dark:bg-blue-900/30',
            ];
            $borderColors = [
                'success' => 'border-green-500',
                'error' => 'border-red-500',
                'warning' => 'border-yellow-500',
                'info' => 'border-blue-500',
            ];
            $textColors = [
                'success' => 'text-green-800 dark:text-green-200',
                'error' => 'text-red-800 dark:text-red-200',
                'warning' => 'text-yellow-800 dark:text-yellow-200',
                'info' => 'text-blue-800 dark:text-blue-200',
            ];
            $iconColors = [
                'success' => 'text-green-500',
                'error' => 'text-red-500',
                'warning' => 'text-yellow-500',
                'info' => 'text-blue-500',
            ];

            $type = $notification['type'] ?? 'info';
        @endphp

        <div
            x-data="{ show: true }"
            x-init="setTimeout(() => show = false, 5000); $watch('show', value => !value && $wire.removeNotification('{{ $notification['id'] }}'))"
            x-show="show"
            x-transition:enter="transform ease-out duration-300 transition"
            x-transition:enter-start="-translate-y-4 opacity-0"
            x-transition:enter-end="translate-y-0 opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="w-full max-w-md {{ $bgColors[$type] }} border-l-4 {{ $borderColors[$type] }} rounded-r-lg shadow-lg pointer-events-auto"
        >
            <div class="p-4">
                <div class="flex items-start">
                    <!-- Иконка -->
                    <div class="flex-shrink-0 {{ $iconColors[$type] }}">
                        @if($type === 'success')
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        @elseif($type === 'error')
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        @elseif($type === 'warning')
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        @else
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        @endif
                    </div>

                    <!-- Текст уведомления -->
                    <div class="ml-3 w-0 flex-1">
                        <p class="text-sm font-medium {{ $textColors[$type] }}">
                            {{ $notification['message'] }}
                        </p>
                        <p class="mt-1 text-xs {{ $type === 'warning' ? 'text-yellow-600 dark:text-yellow-300' : 'text-opacity-70 dark:text-opacity-70' }}">
                            {{ $notification['time']->diffForHumans() }}
                        </p>
                    </div>

                    <!-- Кнопка закрытия -->
                    <div class="ml-4 flex-shrink-0 flex">
                        <button
                            @click="show = false"
                            class="{{ $bgColors[$type] }} rounded-md inline-flex text-gray-500 hover:text-gray-700 focus:outline-none"
                        >
                            <span class="sr-only">Закрыть</span>
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
