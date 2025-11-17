<?php

namespace App\Http\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;


class UsersTable extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'id';
    public $sortDirection = 'asc';
    public $perPage = 10;
    public $filterRole = '';
    public $confirmingDelete = false;
    public $userToDelete = null;

    protected $queryString = ['search', 'sortField', 'sortDirection', 'filterRole'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterRole()
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

    public function confirmDelete($id)
{
    if ($id == Auth::user()->id) {
        session()->flash('error', 'Não pode eliminar a sua própria conta!');
        return;
    }

    $this->userToDelete = $id;
    $this->confirmingDelete = true;
}


    public function delete()
{
    if ($this->userToDelete) {
        $user = User::find($this->userToDelete);

        if ($user && $user->id !== Auth::user()->id) {
            $user->delete();
            session()->flash('success', 'Utilizador eliminado com sucesso!');
        }
    }

    $this->confirmingDelete = false;
    $this->userToDelete = null;
}


    public function render()
    {
        $users = User::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterRole, function ($query) {
                $query->where('role', $this->filterRole);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.users-table', [
            'users' => $users,
        ]);
    }
}
