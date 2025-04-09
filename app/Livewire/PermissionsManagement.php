<?php

namespace App\Livewire;

use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Livewire\Attributes\On;

class PermissionsManagement extends Component
{
    public $permissions;
    public bool $showPermissionModal = false;
    public ?Permission $selectedPermission = null;
    public string $permissionName = '';

    #[On('refreshPermissions')]
    public function mount(): void
    {
        $this->loadPermissions();
    }

    public function loadPermissions(): void
    {
        $this->permissions = Permission::all();
    }

    public function createPermission(): void
    {
        $this->selectedPermission = null;
        $this->permissionName = '';
        $this->showPermissionModal = true;
    }

    public function editPermission(int $permissionId): void
    {
        $this->selectedPermission = Permission::find($permissionId);
        $this->permissionName = $this->selectedPermission->name;
        $this->showPermissionModal = true;
    }

    public function savePermission(): void
    {
        $this->validate([
            'permissionName' => 'required|string|max:255|unique:permissions,name' .
                ($this->selectedPermission ? ',' . $this->selectedPermission->id : ''),
        ]);

        if ($this->selectedPermission) {
            $this->selectedPermission->update(['name' => $this->permissionName]);
            $message = 'Дозвіл успішно оновлено.';
        } else {
            Permission::create(['name' => $this->permissionName]);
            $message = 'Дозвіл успішно створено.';
        }

        $this->closePermissionModal();
        $this->loadPermissions();

        session()->flash('message', $message);
        $this->dispatch('notify', ['message' => $message, 'type' => 'success']);
    }

    public function deletePermission(int $permissionId): void
    {
        $permission = Permission::find($permissionId);

        if ($permission) {
            $permission->delete();
            $this->loadPermissions();

            session()->flash('message', 'Дозвіл успішно видалено.');
            $this->dispatch('notify', [
                'message' => 'Дозвіл успішно видалено.',
                'type' => 'success'
            ]);
        }
    }

    public function closePermissionModal(): void
    {
        $this->showPermissionModal = false;
        $this->selectedPermission = null;
        $this->permissionName = '';
    }

    public function render()
    {
        return view('livewire.permissions-management');
    }
}
