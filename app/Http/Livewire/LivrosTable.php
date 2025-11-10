<?php

namespace App\Http\Livewire;

use App\Models\Livro;
use Livewire\Component;
use Livewire\WithPagination;

class LivrosTable extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'nome';
    public $sortDirection = 'asc';
    public $perPage = 10;
    public $filterEditora = '';

    protected $queryString = ['search', 'sortField', 'sortDirection'];

    public function updatingSearch()
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

    public function delete($id)
    {
        Livro::find($id)->delete();
        session()->flash('success', 'Livro excluído com sucesso!');
    }

    public function render()
    {
        $livros = Livro::with(['editora', 'autores'])
            ->when($this->search, function ($query) {
                $query->where('nome', 'like', '%' . $this->search . '%')
                    ->orWhere('isbn', 'like', '%' . $this->search . '%');
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
