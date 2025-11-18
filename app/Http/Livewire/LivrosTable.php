<?php

namespace App\Http\Livewire;

use App\Models\Livro;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class LivrosTable extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'nome';
    public $sortDirection = 'asc';
    public $perPage = 10;
    public $filterEditora = '';
    public $confirmingDelete = false;
    public $livroToDelete = null;
    public $isAdmin = false;

    protected $queryString = ['search', 'sortField', 'sortDirection', 'filterEditora'];

    public function mount()
    {
        $this->isAdmin = optional(Auth::user())->isAdmin();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterEditora()
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
        $this->livroToDelete = $id;
        $this->confirmingDelete = true;
    }

    public function delete()
    {
        if ($this->livroToDelete) {
            $livro = Livro::find($this->livroToDelete);

            if ($livro) {
                $livro->delete();
                session()->flash('success', 'Livro eliminado com sucesso!');
            }
        }

        $this->confirmingDelete = false;
        $this->livroToDelete = null;
    }

    public function render()
{
    $livros = Livro::with(['editora', 'autores', 'requisicoes'])
        ->when($this->search, function ($query) {
            $query->where(function ($q) {
                $q->where('nome', 'like', '%' . $this->search . '%')
                  ->orWhere('isbn', 'like', '%' . $this->search . '%');
            });
        })
        ->when($this->filterEditora, function ($query) {
            $query->where('editora_id', $this->filterEditora);
        })
        ->orderBy($this->sortField, $this->sortDirection)
        ->paginate($this->perPage);

    $editoras = \App\Models\Editora::orderBy('nome')->get();

    return view('livewire.livros-table', [
        'livros' => $livros,
        'editoras' => $editoras,
    ]);
}

}
