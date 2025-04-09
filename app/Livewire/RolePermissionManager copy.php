<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class RolePermissionManager extends Component
{
    public $users;
    public $roles;
    public $permissions;
    public $activeTab = 'users';


    public bool $showUserRolesModal = false; // Показувати чи ховати модальне вікно
    public ?User $selectedUser = null; // Користувач, що редагується
    public $userRoles = []; // Масив ID ролей, вибраних для користувача у модальному вікні

    public bool $showRolePermissionsModal = false; // Стан видимості модального вікна
    public ?Role $selectedRole = null; // Роль, що редагується
    public $rolePermissions = []; // Масив ID дозволів, вибраних для ролі

    // --- Властивості для модального вікна ефективних дозволів користувача ---
    public bool $showUserEffectivePermissionsModal = false;
    public $effectivePermissions = []; // Для зберігання списку дозволів


    public function mount(): void
    {
        $this->loadData();
    }

    public function loadData(): void
    {
        $this->users = User::with('roles')->get();
        $this->roles = Role::with('permissions')->get();
        $this->permissions = Permission::all();
    }

    public function switchTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    // --- Нові методи для модального вікна користувача ---

    /**
     * Відкриває модальне вікно для редагування ролей користувача.
     * @param int $userId ID користувача
     */
    public function editUserRoles(int $userId): void
    {
        // Знаходимо користувача разом з його поточними ролями
        $this->selectedUser = User::with('roles')->find($userId);

        // Якщо користувача не знайдено, нічого не робимо
        if (!$this->selectedUser) {
            return;
        }

        // Отримуємо масив ID поточних ролей користувача
        $this->userRoles = $this->selectedUser->roles->pluck('id')->map(fn($id) => (string)$id)->toArray();
        // Показуємо модальне вікно
        $this->showUserRolesModal = true;
    }

    /**
     * Закриває модальне вікно редагування ролей користувача.
     */
    public function closeUserRolesModal(): void
    {
        $this->showUserRolesModal = false;
        $this->selectedUser = null; // Скидаємо вибраного користувача
        $this->userRoles = []; // Скидаємо вибрані ролі
    }

    /**
     * Оновлює ролі для вибраного користувача.
     */
    public function updateUserRoles(): void
    {
        // Перевіряємо, чи вибраний користувач
        if ($this->selectedUser) {
            // 1. Отримуємо колекцію моделей Role на основі вибраних ID ($this->userRoles)
            $rolesToSync = Role::whereIn('id', $this->userRoles)->get();

            // 2. Синхронізуємо ролі користувача з отриманою колекцією ролей
            // Метод syncRoles приймає колекцію моделей Role або масив імен ролей.
            $this->selectedUser->syncRoles($rolesToSync);

            // Зберігаємо ім'я перед закриттям модального вікна
            $userName = $this->selectedUser->name;

            // Закриваємо модальне вікно
            $this->closeUserRolesModal();
            // Оновлюємо список користувачів, щоб побачити зміни
            $this->loadData();

            // Можна додати повідомлення про успіх (наприклад, за допомогою session flash)
            // Використовуємо збережене ім'я
            session()->flash('message', 'Ролі користувача ' . $userName . ' успішно оновлено.');
            // Очищаємо повідомлення через короткий час, якщо потрібно
            $this->dispatch('notify', ['message' => 'Ролі користувача ' . $userName . ' успішно оновлено.', 'type' => 'success']); // Приклад для кастомного JS слухача
        }
    }
    // --- Методи для модального вікна ролі ---

    /**
     * Відкриває модальне вікно для редагування дозволів ролі.
     * @param int $roleId ID ролі
     */
    public function editRolePermissions(int $roleId): void
    {
        // Знаходимо роль разом з її поточними дозволами
        $this->selectedRole = Role::with('permissions')->find($roleId);

        if (! $this->selectedRole) {
            return;
        }

        // Отримуємо масив ID поточних дозволів ролі
        $this->rolePermissions = $this->selectedRole->permissions->pluck('id')->map(fn($id) => (string)$id)->toArray();
        // Відкриваємо модальне вікно
        $this->showRolePermissionsModal = true;
    }

    /**
     * Закриває модальне вікно редагування дозволів ролі.
     */
    public function closeRolePermissionsModal(): void
    {
        $this->showRolePermissionsModal = false;
        $this->selectedRole = null; // Скидаємо вибрану роль
        $this->rolePermissions = []; // Скидаємо вибрані дозволи
    }

    public function updateRolePermissions(): void
    {
        if ($this->selectedRole) {
            // Отримуємо моделі Permission на основі вибраних ID
            $permissionsToSync = Permission::whereIn('id', $this->rolePermissions)->get();

            // Синхронізуємо дозволи ролі
            $this->selectedRole->syncPermissions($permissionsToSync);

            $roleName = $this->selectedRole->name; // Зберігаємо ім'я

            $this->closeRolePermissionsModal(); // Закриваємо вікно
            $this->loadData(); // Оновлюємо дані таблиць

            // Сповіщення про успіх
            session()->flash('message', 'Дозволи для ролі ' . $roleName . ' успішно оновлено.');
            $this->dispatch('notify', ['message' => 'Дозволи для ролі ' . $roleName . ' успішно оновлено.', 'type' => 'success']);
        }
    }

    // --- Методи для модального вікна ефективних дозволів ---

    /**
     * Завантажує ефективні дозволи користувача та відкриває модальне вікно.
     * @param int $userId
     */
    public function viewUserEffectivePermissions(int $userId): void
    {
        $this->selectedUser = User::find($userId); // Знаходимо користувача

        if (! $this->selectedUser) {
            return;
        }

        // Отримуємо ВСІ дозволи користувача (прямі та через ролі)
        // Пакет spatie/laravel-permission надає зручний метод для цього
        $this->effectivePermissions = $this->selectedUser->getAllPermissions();

        $this->showUserEffectivePermissionsModal = true; // Показуємо вікно
    }

    /**
     * Закриває модальне вікно перегляду ефективних дозволів.
     */
    public function closeUserEffectivePermissionsModal(): void
    {
        $this->showUserEffectivePermissionsModal = false;
        $this->selectedUser = null;
        $this->effectivePermissions = [];
    }

    public function render()
    {
        // Передаємо всі доступні ролі у шаблон (для модального вікна)
        return view('livewire.role-permission-manager', [
            'allRoles' => Role::all() // Додаємо змінну з усіма ролями
        ]);
    }
}
