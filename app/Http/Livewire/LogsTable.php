<?php

namespace App\Http\Livewire;

use App\Models\Log;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class LogsTable extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 25;
    public $filterModulo = '';
    public $filterAcao = '';
    public $filterUserId = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $isAdmin = false;

    protected $queryString = [
        'search',
        'sortField',
        'sortDirection',
        'filterModulo',
        'filterAcao',
        'filterUserId',
        'dateFrom',
        'dateTo'
    ];

    public function mount()
    {
        $this->isAdmin = optional(Auth::user())->isAdmin();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterModulo()
    {
        $this->resetPage();
    }

    public function updatingFilterAcao()
    {
        $this->resetPage();
    }

    public function updatingFilterUserId()
    {
        $this->resetPage();
    }

    public function updatingDateFrom()
    {
        $this->resetPage();
    }

    public function updatingDateTo()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortField = $field;
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->filterModulo = '';
        $this->filterAcao = '';
        $this->filterUserId = '';
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->resetPage();
    }

    public function render()
    {
        $logs = Log::with('user')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('descricao', 'like', '%' . $this->search . '%')
                      ->orWhere('ip_address', 'like', '%' . $this->search . '%')
                      ->orWhereHas('user', function ($userQuery) {
                          $userQuery->where('name', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->when($this->filterModulo, function ($query) {
                $query->where('modulo', $this->filterModulo);
            })
            ->when($this->filterAcao, function ($query) {
                $query->where('acao', $this->filterAcao);
            })
            ->when($this->filterUserId, function ($query) {
                $query->where('user_id', $this->filterUserId);
            })
            ->when($this->dateFrom, function ($query) {
                $query->whereDate('created_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function ($query) {
                $query->whereDate('created_at', '<=', $this->dateTo);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $modulos = Log::select('modulo')->distinct()->orderBy('modulo')->pluck('modulo');
        $acoes = Log::select('acao')->distinct()->orderBy('acao')->pluck('acao');
        $users = \App\Models\User::orderBy('name')->get();

        return view('livewire.logs-table', [
            'logs' => $logs,
            'modulos' => $modulos,
            'acoes' => $acoes,
            'users' => $users,
        ]);
    }
}
