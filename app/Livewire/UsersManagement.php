<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Livewire\Attributes\On;

class UsersManagement extends Component
{
    public $users;
    public bool $showUserRolesModal = false;
    public ?User $selectedUser = null;
    public $userRoles = [];
    public bool $showUserEffectivePermissionsModal = false;
    public $effectivePermissions = [];

    #[On('refreshUsers')] // Слухаємо подію для оновлення даних
    public function mount(): void
    {
        $this->loadUsers();
    }

    public function loadUsers(): void
    {
        $this->users = User::with('roles')->get();
    }

    public function editUserRoles(int $userId): void
    {
        $this->selectedUser = User::with('roles')->find($userId);

        if (!$this->selectedUser) {
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
    }

    public function updateUserRoles(): void
    {
        if ($this->selectedUser) {
            $rolesToSync = Role::whereIn('id', $this->userRoles)->get();
            $this->selectedUser->syncRoles($rolesToSync);

            $userName = $this->selectedUser->name;
            $this->closeUserRolesModal();
            $this->loadUsers();

            session()->flash('message', 'Ролі користувача ' . $userName . ' успішно оновлено.');
            $this->dispatch('notify', ['message' => 'Ролі користувача ' . $userName . ' успішно оновлено.', 'type' => 'success']);
        }
    }

    public function viewUserEffectivePermissions(int $userId): void
    {
        $this->selectedUser = User::find($userId);

        if (!$this->selectedUser) {
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
    }

    public function render()
    {
        return view('livewire.users-management', [
            'allRoles' => Role::all()
        ]);
    }
}
