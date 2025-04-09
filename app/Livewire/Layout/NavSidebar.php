<?php

namespace App\Livewire\Layout;

use Livewire\Component;

class NavSidebar extends Component
{
    public bool $isSidebarOpen = true;

    public function toggleSidebar(): void
    {
        $this->isSidebarOpen = !$this->isSidebarOpen;
        // Отправляем событие в браузер с новым состоянием
        $this->dispatch('sidebar-toggled', ['isOpen' => $this->isSidebarOpen]);
    }

    // Метод для початкової синхронізації (не обов'язковий, але може бути корисним)
    // public function syncAlpineState() {
    //     $this->dispatch('sidebar-toggled', isOpen: $this->isSidebarOpen);
    // }
    public function mount()
    {
        // Проверяем localStorage через JavaScript и устанавливаем начальное состояние
        $this->js('
            if (localStorage.getItem("sidebarOpen") !== null) {
                $wire.set("isSidebarOpen", localStorage.getItem("sidebarOpen") === "true");
            }
        ');
    }

    public function render()
    {
        return view('livewire.layout.nav-sidebar');
    }
}
