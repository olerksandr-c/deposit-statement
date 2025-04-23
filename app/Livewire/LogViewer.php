<?php

namespace App\Livewire;

use App\Models\Log;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Maatwebsite\Excel\Concerns\WithTitle;


class LogViewer extends Component
{
    use WithPagination;

    public $title = 'Логування';
    public $userName = '';
    public $perPage = 10;  // Кількість записів на сторінці
    public $dateFilter = 'all'; // За замовчуванням: показати всі записи

    public $logType = '';

    protected $queryString = ['userName', 'perPage', 'dateFilter', 'logType'];

    // Використовуємо лістенери для відстеження змін
    protected $listeners = ['refresh' => '$refresh'];

    public function updatingLogType()
    {
        $this->resetPage();
    }

    // Оновлюємо пагінацію при зміні фільтрів
    public function updatingUserName()
    {
        $this->resetPage();
    }

    public function updatingDateFilter()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->userName = '';
        $this->dateFilter = 'all';
        $this->logType = '';
        $this->resetPage();
    }

    // Метод для отримання логів з фільтрацією та пагінацією
    public function render()
    {
        $user = Auth::user();

        if (!Auth::user()->hasRole('administrator')) {
            abort(403, 'Доступ заборонено');
        }


        // if (!Auth::user()->hasPermissionTo('access-admin-area')) {
        //     abort(403, 'Доступ заборонено');
        // }

        $logs = Log::query()
            ->when(trim($this->userName) !== '', function ($query) {
                $query->whereHas('user', fn($q) => $q->searchByName($this->userName));
            })
            // Filter by date
            ->when($this->dateFilter !== 'all', function ($query) {
                // Date filter logic remains the same
                $now = Carbon::now();
                switch ($this->dateFilter) {
                    case 'day':
                        $query->where('created_at', '>=', $now->copy()->subDay());
                        break;
                    case 'week':
                        $query->where('created_at', '>=', $now->copy()->subWeek());
                        break;
                    case 'month':
                        $query->where('created_at', '>=', $now->copy()->subMonth());
                        break;
                    case 'year':
                        $query->where('created_at', '>=', $now->copy()->subYear());
                        break;
                }
            })
            // Add filter by log type
            ->when(trim($this->logType) !== '', function ($query) {
                $query->where('log_type', $this->logType);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.log-viewer', compact('logs'));
    }
}
