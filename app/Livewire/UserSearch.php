<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\LdapUser;
use LdapRecord\Models\ActiveDirectory\User;
use LdapRecord\Container;

class UserSearch extends Component
{
    public $searchQuery = '';
    public $users = [];
    public $isConnected = false;
    public $connectionError = '';
    public $loading = false;

    public function mount()
    {
        $this->checkConnection();
    }

    public function checkConnection()
    {
        try {
            // Проверяем подключение к LDAP-серверу
            Container::getDefaultConnection()->connect();
            $this->isConnected = true;
        } catch (\Exception $e) {
            $this->connectionError = 'Ошибка подключения к LDAP: ' . $e->getMessage();
            $this->isConnected = false;
        }
    }

    public function search()
    {
        if (empty($this->searchQuery)) {
            $this->users = [];
            return;
        }

        $this->loading = true;

        try {
            // Выполняем поиск пользователей по введенному запросу
            $this->users = User::query()
                ->select(['cn', 'samaccountname', 'mail', 'displayname', 'telephonenumber', 'title', 'department'])
                ->orWhere('cn', 'contains', $this->searchQuery)
                ->orWhere('samaccountname', 'contains', $this->searchQuery)
                ->orWhere('mail', 'contains', $this->searchQuery)
                ->orWhere('displayname', 'contains', $this->searchQuery)
                ->get()
                ->toArray();
        } catch (\Exception $e) {
            $this->connectionError = 'Ошибка поиска: ' . $e->getMessage();
        }
        // dd($this->users);
        $this->loading = false;
    }

    public function render()
    {
        
        
        return view('livewire.user-search');
        
    }
}
