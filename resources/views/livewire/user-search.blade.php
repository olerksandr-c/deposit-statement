<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-6">Поиск пользователей в Active Directory</h1>

    @if (!$isConnected)
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <p>{{ $connectionError }}</p>
        </div>
    @else
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <p>Подключение к LDAP успешно установлено</p>
        </div>

        <div class="mb-6">
            <div class="flex">
                <input wire:model.live.debounce.300ms="searchQuery"
                       wire:keydown.enter="search"
                       type="text"
                       placeholder="Введите имя пользователя, логин или email..."
                       class="flex-1 p-2 border rounded-l">

                <button wire:click="search"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-r">
                    Поиск
                </button>
            </div>
        </div>

        @if ($loading)
            <div class="text-center">
                <p>Загрузка...</p>
            </div>
        @else
            @if (count($users) > 0)
                <table class="min-w-full bg-white border">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b">Имя</th>
                            <th class="py-2 px-4 border-b">Логин</th>
                            <th class="py-2 px-4 border-b">Email</th>
                            <th class="py-2 px-4 border-b">Отображаемое имя</th>
                            <th class="py-2 px-4 border-b">Телефон</th>
                            <th class="py-2 px-4 border-b">Должность</th>
                            <th class="py-2 px-4 border-b">Отдел</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td class="py-2 px-4 border-b">{{ $user['cn'][0] ?? '-' }}</td>
                                <td class="py-2 px-4 border-b">{{ $user['samaccountname'][0] ?? '-' }}</td>
                                <td class="py-2 px-4 border-b">{{ $user['mail'][0] ?? '-' }}</td>
                                <td class="py-2 px-4 border-b">{{ $user['displayname'][0] ?? '-' }}</td>
                                <td class="py-2 px-4 border-b">{{ $user['telephonenumber'][0] ?? '-' }}</td>
                                <td class="py-2 px-4 border-b">{{ $user['title'][0] ?? '-' }}</td>
                                <td class="py-2 px-4 border-b">{{ $user['department'][0] ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @elseif ($searchQuery)
                <div class="text-center">
                    <p>Пользователи не найдены</p>
                </div>
            @endif
        @endif
    @endif
</div>
