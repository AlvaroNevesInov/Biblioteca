<?php

namespace App\Http\Livewire;

use App\Models\Livro;
use App\Services\GoogleBooksService;
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
    public $searchMode = 'local'; // 'local' ou 'api'
    public $apiResults = [];
    public $apiTotalResults = 0;
    public $apiCurrentPage = 1;

    protected $queryString = ['search', 'sortField', 'sortDirection', 'filterEditora', 'searchMode'];

    public function mount()
    {
        $this->isAdmin = optional(Auth::user())->isAdmin();
    }

    public function updatingSearch()
    {
        $this->resetPage();
        $this->apiCurrentPage = 1;
    }

    public function updatingFilterEditora()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function updatingSearchMode()
    {
        $this->resetPage();
        $this->apiCurrentPage = 1;
        $this->apiResults = [];
    }

    public function switchToLocal()
    {
        $this->searchMode = 'local';
        $this->resetPage();
    }

    public function switchToApi()
    {
        $this->searchMode = 'api';
        $this->apiCurrentPage = 1;
        $this->apiResults = [];
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

    public function searchGoogleBooks($page = 1)
    {
        if (empty($this->search)) {
            $this->apiResults = [];
            $this->apiTotalResults = 0;
            return;
        }

        $googleBooksService = app(GoogleBooksService::class);
        $maxResults = 20;
        $startIndex = ($page - 1) * $maxResults;

        $result = $googleBooksService->search($this->search, $maxResults, $startIndex);

        if ($result['success']) {
            $this->apiResults = $result['items'];
            $this->apiTotalResults = $result['totalItems'];
            $this->apiCurrentPage = $page;
        } else {
            $this->apiResults = [];
            $this->apiTotalResults = 0;
            session()->flash('error', $result['error'] ?? 'Erro ao pesquisar na Google Books API');
        }
    }

    public function nextApiPage()
    {
        $this->searchGoogleBooks($this->apiCurrentPage + 1);
    }

    public function previousApiPage()
    {
        if ($this->apiCurrentPage > 1) {
            $this->searchGoogleBooks($this->apiCurrentPage - 1);
        }
    }

    public function render()
    {
        // Se está em modo API e tem pesquisa, busca da API
        if ($this->searchMode === 'api' && !empty($this->search)) {
            // Se ainda não pesquisou ou mudou a pesquisa, faz a busca
            if (empty($this->apiResults)) {
                $this->searchGoogleBooks(1);
            }
        }

        // Busca livros locais
        $livros = Livro::with(['editora', 'autores', 'requisicoes'])
            ->when($this->search && $this->searchMode === 'local', function ($query) {
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
