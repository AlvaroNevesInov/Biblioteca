<?php

namespace App\Http\Livewire;

use App\Models\Review;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class ReviewsTable extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;
    public $filterEstado = 'suspenso';
    public $confirmingDelete = false;
    public $reviewToDelete = null;
    public $isAdmin = false;

    protected $queryString = ['search', 'sortField', 'sortDirection', 'filterEstado'];

    public function mount()
    {
        $this->isAdmin = optional(Auth::user())->isAdmin();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterEstado()
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
        $this->reviewToDelete = $id;
        $this->confirmingDelete = true;
    }

    public function delete()
    {
        if ($this->reviewToDelete) {
            $review = Review::find($this->reviewToDelete);

            if ($review) {
                // Apenas admin pode eliminar reviews
                if ($this->isAdmin) {
                    $review->delete();
                    session()->flash('success', 'Review eliminado com sucesso!');
                } else {
                    session()->flash('error', 'Não tem permissão para eliminar reviews.');
                }
            }
        }

        $this->confirmingDelete = false;
        $this->reviewToDelete = null;
    }

    public function render()
    {
        $query = Review::with(['user', 'livro', 'requisicao']);

        // Aplicar pesquisa
        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('livro', function ($livroQuery) {
                    $livroQuery->where('nome', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('user', function ($userQuery) {
                    $userQuery->where('name', 'like', '%' . $this->search . '%');
                })
                ->orWhere('comentario', 'like', '%' . $this->search . '%');
            });
        }

        // Aplicar filtro de estado
        if ($this->filterEstado) {
            $query->where('estado', $this->filterEstado);
        }

        // Aplicar ordenação
        $query->orderBy($this->sortField, $this->sortDirection);

        $reviews = $query->paginate($this->perPage);

        // Calcular indicadores
        $reviewsSuspensos = Review::where('estado', 'suspenso')->count();
        $reviewsAtivos = Review::where('estado', 'ativo')->count();
        $reviewsRecusados = Review::where('estado', 'recusado')->count();

        return view('livewire.reviews-table', [
            'reviews' => $reviews,
            'reviewsSuspensos' => $reviewsSuspensos,
            'reviewsAtivos' => $reviewsAtivos,
            'reviewsRecusados' => $reviewsRecusados,
        ]);
    }
}
