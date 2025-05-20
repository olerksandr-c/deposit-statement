<?php

namespace App\Livewire;

use App\Models\User; // Модель користувача вашого додатку
use Spatie\Permission\Models\Role;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use LdapRecord\Models\ActiveDirectory\User as LdapUserRecord; // Модель користувача LDAP
use LdapRecord\Container as LdapContainer; // Контейнер для з'єднання LDAP
use Exception; // Для обробки винятків
use Illuminate\Support\Facades\Event;


class UsersManagement extends Component
{
    public $users;
    public bool $showUserRolesModal = false;
    public ?User $selectedUser = null;
    public $userRoles = [];
    public bool $showUserEffectivePermissionsModal = false;
    public $effectivePermissions = [];

    // Властивості для модального вікна пошуку LDAP
    public bool $showLdapSearchModal = false;
    public string $ldapSearchQuery = '';
    public array $ldapUsers = [];
    public bool $ldapLoading = false;
    public string $ldapConnectionError = '';
    public bool $ldapIsConnected = false;
    public ?array $ldapSelectedAttributes = ['objectguid', 'cn', 'samaccountname', 'mail', 'displayname', 'department', 'title', 'telephonenumber'];
    public bool $showDeleteConfirmModal = false;
    public ?int $userIdToDelete = null;





    #[On('openDeleteConfirmation')]
    public function openDeleteConfirmModal(int $userId): void
    {
        $this->userIdToDelete = $userId;
        $this->showDeleteConfirmModal = true;
    }
    // Закриття модального вікна
    public function closeDeleteConfirmModal(): void
    {
        $this->showDeleteConfirmModal = false;
        $this->userIdToDelete = null;
    }

    // Метод для видалення користувача
    public function deleteUser(): void
    {
        if (!$this->userIdToDelete) {
            session()->flash('error', 'Не вказано ID користувача.');
            return;
        }

        $user = User::find($this->userIdToDelete);
        if (!$user) {
            session()->flash('error', 'Користувача не знайдено.');
            return;
        }

        try {
            $userName = $user->name;
            $user->delete();
            session()->flash('message', "Користувача {$userName} успішно видалено.");
            $this->loadUsers(); // Оновлюємо список користувачів
        } catch (\Exception $e) {
            session()->flash('error', "Помилка при видаленні: " . $e->getMessage());
        }

        $this->closeDeleteConfirmModal();
    }


    #[On('refreshUsers')] // Слухаємо подію для оновлення даних
    public function mount(): void
    {
        $this->loadUsers();
    }


    // Завантаження списку локальних користувачів
    public function loadUsers(): void
    {
        $this->users = User::with('roles')->get();
    }

    // Редагування ролей існуючого користувача (ваш існуючий код)
    public function editUserRoles(int $userId): void
    {
        $this->selectedUser = User::with('roles')->find($userId);
        if (!$this->selectedUser) {
            session()->flash('error', 'Користувача не знайдено.'); // Повідомлення про помилку
            return;
        }
        $this->userRoles = $this->selectedUser->roles->pluck('id')->map(fn($id) => (string)$id)->toArray();
        $this->showUserRolesModal = true;
    }

    public function closeUserRolesModal(): void
    {
        $this->showUserRolesModal = false;
        $this->selectedUser = null;
        $this->userRoles = [];
        session()->forget('message'); // Очищення повідомлення
    }

    public function updateUserRoles(): void
    {
        if ($this->selectedUser) {
            // Валідація: переконатися, що ролі існують
            $validRoles = Role::whereIn('id', $this->userRoles)->pluck('id')->toArray();
            $this->selectedUser->syncRoles($validRoles);

            $userName = $this->selectedUser->name;
            $this->closeUserRolesModal();
            $this->loadUsers();

            session()->flash('message', 'Ролі користувача ' . $userName . ' успішно оновлено.');
            $this->dispatch('notify', ['message' => 'Ролі користувача ' . $userName . ' успішно оновлено.', 'type' => 'success']);
        }
    }

    // Перегляд ефективних дозволів (ваш існуючий код)
    public function viewUserEffectivePermissions(int $userId): void
    {
        $this->selectedUser = User::find($userId);
        if (!$this->selectedUser) {
            session()->flash('error', 'Користувача не знайдено.');
            return;
        }
        $this->effectivePermissions = $this->selectedUser->getAllPermissions();
        $this->showUserEffectivePermissionsModal = true;
    }

    public function closeUserEffectivePermissionsModal(): void
    {
        $this->showUserEffectivePermissionsModal = false;
        $this->selectedUser = null;
        $this->effectivePermissions = [];
        session()->forget('message');
    }

    // --- Методи для роботи з LDAP ---

    // Перевірка з'єднання з LDAP-сервером
    public function checkLdapConnection(): void
    {
        $this->ldapConnectionError = ''; // Скидаємо попередню помилку
        try {
            if (LdapContainer::getDefaultConnection()) {
                LdapContainer::getDefaultConnection()->connect();
                $this->ldapIsConnected = true;
            } else {
                $this->ldapIsConnected = false;
                $this->ldapConnectionError = 'LDAP з\'єднання не налаштовано в конфігурації LdapRecord.';
            }
        } catch (Exception $e) {
            $this->ldapConnectionError = 'Помилка підключення до LDAP: ' . $e->getMessage();
            $this->ldapIsConnected = false;
        }
    }

    // Відкриття модального вікна пошуку LDAP
    public function openLdapSearchModal(): void
    {
        $this->resetLdapSearchData(); // Скидання попередніх даних
        $this->checkLdapConnection(); // Перевірка з'єднання при кожному відкритті
        $this->showLdapSearchModal = true;
    }

    // Закриття модального вікна пошуку LDAP
    public function closeLdapSearchModal(): void
    {
        $this->showLdapSearchModal = false;
        $this->resetLdapSearchData();
        session()->forget('ldap_message'); // Очищення повідомлень LDAP
        session()->forget('ldap_error');
    }

    // Скидання даних, пов'язаних з пошуком LDAP
    private function resetLdapSearchData(): void
    {
        $this->ldapSearchQuery = '';
        $this->ldapUsers = [];
        $this->ldapLoading = false;
        // $this->ldapConnectionError = ''; // Не скидаємо помилку з'єднання, щоб користувач її бачив
        // $this->ldapIsConnected = false; // Стан з'єднання перевіряється при відкритті
    }

    // Пошук користувачів в LDAP
    public function searchLdapUsers(): void
    {
        if (!$this->ldapIsConnected) {
            $this->ldapConnectionError = 'Неможливо виконати пошук: відсутнє підключення до LDAP.';
            $this->ldapUsers = [];
            return;
        }

        if (empty(trim($this->ldapSearchQuery))) {
            $this->ldapUsers = [];
            return;
        }

        $this->ldapLoading = true;
        $this->ldapUsers = []; // Очищення попередніх результатів
        $this->ldapConnectionError = ''; // Очищення попередніх помилок пошуку

        try {
            // Виконуємо пошук користувачів за введеним запитом
            // Обираємо атрибути, які нам потрібні
            $query = LdapUserRecord::query()->select($this->ldapSelectedAttributes);

            // Додаємо умови пошуку (можна розширити за потреби)
            // $searchTerm = '*' . $this->ldapSearchQuery . '*'; // Пошук з wildcard
            $searchTerm = $this->ldapSearchQuery;

            $query->orWhere('cn', 'contains', $searchTerm)
                ->orWhere('samaccountname', 'contains', $searchTerm)
                ->orWhere('mail', 'contains', $searchTerm)
                ->orWhere('displayname', 'contains', $searchTerm);

            $this->ldapUsers = $query->limit(20)->get()->toArray(); // Обмеження результатів для продуктивності

        } catch (Exception $e) {
            $this->ldapConnectionError = 'Помилка пошуку в LDAP: ' . $e->getMessage();
            $this->ldapUsers = []; // Очищаємо користувачів у разі помилки
        }

        $this->ldapLoading = false;
    }

    // Імпорт користувача з LDAP до локальної бази даних
    public function importLdapUser(string $ldapUserGuid): void
    {
        if (!$this->ldapIsConnected) {
            session()->flash('ldap_error', 'Неможливо імпортувати: відсутнє підключення до LDAP.');
            return;
        }

        $ldapUserData = null;
        foreach ($this->ldapUsers as $user) {
            if (isset($user['objectguid'][0]) && $user['objectguid'][0] === $ldapUserGuid) {
                $ldapUserData = $user;
                break;
            }
        }

        if (!$ldapUserData) {
            session()->flash('ldap_error', 'Не вдалося знайти дані користувача LDAP для імпорту.');
            return;
        }

        // Перевірка, чи користувач з таким guid вже існує
        // Для цього у вашій моделі User має бути поле guid (або аналогічне)
        // І воно має бути $fillable або $guarded відповідно
        if (property_exists(User::class, 'guid') || (new User)->isFillable('guid')) {
            if (User::where('guid', $ldapUserGuid)->exists()) {
                session()->flash('ldap_error', 'Користувач з таким LDAP GUID вже існує в системі.');
                return;
            }
        }


        $email = $ldapUserData['mail'][0] ?? null;
        $samAccountName = $ldapUserData['samaccountname'][0] ?? null;

        // Перевірка, чи користувач з такою поштою вже існує (якщо пошта є)
        if ($email && User::where('email', $email)->exists()) {
            session()->flash('ldap_error', 'Користувач з email ' . $email . ' вже існує в системі.');
            return;
        }

        // Перевірка, чи користувач з таким samaccountname вже існує (якщо є поле username у моделі User)
        // Припустимо, що поле для samaccountname у вашій моделі називається 'username'
        if ($samAccountName && (property_exists(User::class, 'username') || (new User)->isFillable('username'))) {
            if (User::where('username', $samAccountName)->exists()) {
                session()->flash('ldap_error', 'Користувач з логіном ' . $samAccountName . ' вже існує в системі.');
                return;
            }
        }


        try {
            $newUser = User::create([
                'name' => $ldapUserData['displayname'][0] ?? $ldapUserData['cn'][0] ?? $samAccountName ?? 'LDAP User ' . Str::random(5),
                'email' => $email, // Може бути null, якщо дозволено вашою БД та валідацією моделі
                'password' => Hash::make(Str::random(32)), // Генерація випадкового пароля
                // Додайте це поле до вашої моделі User та міграції
                // 'guid' => $ldapUserGuid,
                // 'username' => $samAccountName, // Якщо є поле username
                // 'email_verified_at' => now(), // Якщо потрібно одразу підтвердити пошту
            ]);

            // Додавання guid та username, якщо поля існують в моделі User
            if (property_exists($newUser, 'guid') || $newUser->isFillable('guid')) {
                $newUser->guid = $ldapUserGuid;
            }
            if ($samAccountName && (property_exists($newUser, 'username') || $newUser->isFillable('username'))) {
                $newUser->username = $samAccountName;
            }
            // Можливо, знадобиться повторне збереження, якщо поля не масово присвоювані
            if ($newUser->isDirty('guid') || ($samAccountName && $newUser->isDirty('username'))) {
                $newUser->save();
            }


            $this->loadUsers(); // Оновлення основного списку користувачів
            $this->closeLdapSearchModal(); // Закриття модального вікна LDAP

            session()->flash('message', 'Користувача ' . $newUser->name . ' успішно імпортовано з LDAP.');
            $this->dispatch('notify', ['message' => 'Користувача ' . $newUser->name . ' успішно імпортовано.', 'type' => 'success']);
        } catch (Exception $e) {
            // Обробка помилок валідації або збереження
            report($e); // Логування помилки
            session()->flash('ldap_error', 'Помилка при створенні користувача: ' . $e->getMessage());
        }
    }


    public function render()
    {
        return view('livewire.users-management', [
            'allRoles' => Role::all(),
        ]);
    }
}
