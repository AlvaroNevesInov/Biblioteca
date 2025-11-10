<?php

namespace App\Http\Livewire;

use App\Models\Editora;
use Livewire\Component;
use Livewire\WithPagination;

class EditorasTable extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'nome';
    public $sortDirection = 'asc';
    public $perPage = 10;

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
        Editora::find($id)->delete();
        session()->flash('success', 'Editora excluída com sucesso!');
    }

    public function render()
    {
        $editoras = Editora::withCount('livros')
            ->when($this->search, function ($query) {
                $query->where('nome', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.editoras-table', [
            'editoras' => $editoras,
        ]);
    }
}
