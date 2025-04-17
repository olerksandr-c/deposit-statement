<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Collection;

class Notifications extends Component
{
    public Collection $notifications;

    protected $listeners = ['notify' => 'addNotification'];

    public function mount()
    {
        $this->notifications = collect();
    }

    public function addNotification($message, $type = 'info')
    {
        // Добавляем проверку на тип параметров
        if (is_array($message)) {
            // Если передали массив
            $notification = $message;
            $type = $notification['type'] ?? 'info';
            $message = $notification['message'] ?? '';
        }

        $this->notifications->push([
            'id' => uniqid(),
            'message' => $message,
            'type' => $type,
            'time' => now(),
        ]);

        // Проверяем, есть ли элементы в коллекции, прежде чем вызывать end()
        if ($this->notifications->isNotEmpty()) {
            $lastNotification = $this->notifications->last();
            $this->dispatch('remove-notification', id: $lastNotification['id'], delay: 5000);
        }
    }

    public function removeNotification($id)
    {
        $this->notifications = $this->notifications->reject(function ($notification) use ($id) {
            return $notification['id'] === $id;
        });
    }

    public function render()
    {
        return view('livewire.notifications');
    }
}
