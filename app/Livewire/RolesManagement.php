<?php

namespace App\Livewire;

use Livewire\Component;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Livewire\Attributes\On;

class RolesManagement extends Component
{
    public $roles;
    public $permissions;
    public bool $showRolePermissionsModal = false;
    public bool $showRoleCreateModal = false; // Нова модалка для створення ролі
    public ?Role $selectedRole = null;
    public $rolePermissions = [];
    public string $newRoleName = ''; // Для створення нової ролі

    #[On('refreshRoles')]
    public function mount(): void
    {
        $this->loadRoles();
        $this->permissions = Permission::all();
    }

    public function loadRoles(): void
    {
        $this->roles = Role::with('permissions')->get();
    }

    // --- Методи для створення ролі ---
    public function createRole(): void
    {
        $this->validate([
            'newRoleName' => 'required|string|max:255|unique:roles,name',
        ]);

        Role::create(['name' => $this->newRoleName]);
        $this->closeRoleCreateModal();
        $this->loadRoles();

        session()->flash('message', 'Роль успішно створена.');
        $this->dispatch('notify', [
            'message' => 'Роль успішно створена.',
            'type' => 'success'
        ]);
    }

    public function openRoleCreateModal(): void
    {
        $this->newRoleName = '';
        $this->showRoleCreateModal = true;
    }

    public function closeRoleCreateModal(): void
    {
        $this->showRoleCreateModal = false;
    }

    // --- Методи для видалення ролі ---
    public function deleteRole(int $roleId): void
    {
        $role = Role::find($roleId);

        if ($role) {
            // Додаткова перевірка, щоб не видалити системні ролі (наприклад, 'admin')
            if (in_array($role->name, ['admin', 'super-admin'])) {
                $this->dispatch('notify', [
                    'message' => 'Цю роль не можна видалити.',
                    'type' => 'error'
                ]);
                return;
            }

            $role->delete();
            $this->loadRoles();

            session()->flash('message', 'Роль успішно видалена.');
            $this->dispatch('notify', [
                'message' => 'Роль успішно видалена.',
                'type' => 'success'
            ]);
        }
    }

    public function editRolePermissions(int $roleId): void
    {
        $this->selectedRole = Role::with('permissions')->find($roleId);

        if (!$this->selectedRole) {
            return;
        }

        $this->rolePermissions = $this->selectedRole->permissions
            ->pluck('id')
            ->map(fn($id) => (string)$id)
            ->toArray();

        $this->showRolePermissionsModal = true;
    }

    public function closeRolePermissionsModal(): void
    {
        $this->showRolePermissionsModal = false;
        $this->selectedRole = null;
        $this->rolePermissions = [];
    }

    public function updateRolePermissions(): void
    {
        if ($this->selectedRole) {
            $permissionsToSync = Permission::whereIn('id', $this->rolePermissions)->get();
            $this->selectedRole->syncPermissions($permissionsToSync);

            $roleName = $this->selectedRole->name;
            $this->closeRolePermissionsModal();
            $this->loadRoles();

            session()->flash('message', 'Дозволи для ролі ' . $roleName . ' успішно оновлено.');
            $this->dispatch('notify', [
                'message' => 'Дозволи для ролі ' . $roleName . ' успішно оновлено.',
                'type' => 'success'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.roles-management');
    }
}
