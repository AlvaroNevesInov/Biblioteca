<?php

namespace App\Http\Livewire;

use App\Models\Requisicao;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class RequisicoesTable extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'data_requisicao';
    public $sortDirection = 'desc';
    public $perPage = 10;
    public $filterEstado = '';
    public $confirmingDelete = false;
    public $requisicaoToDelete = null;
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
        $this->requisicaoToDelete = $id;
        $this->confirmingDelete = true;
    }

    public function delete()
    {
        if ($this->requisicaoToDelete) {
            $requisicao = Requisicao::find($this->requisicaoToDelete);

            if ($requisicao) {
                // Verificar permissões
                if ($this->isAdmin || $requisicao->user_id === Auth::id()) {
                    // Apenas pode eliminar se estiver pendente
                    if ($requisicao->isPendente()) {
                        $requisicao->delete();
                        session()->flash('success', 'Requisição cancelada com sucesso!');
                    } else {
                        session()->flash('error', 'Apenas requisições pendentes podem ser canceladas.');
                    }
                } else {
                    session()->flash('error', 'Não tem permissão para cancelar esta requisição.');
                }
            }
        }

        $this->confirmingDelete = false;
        $this->requisicaoToDelete = null;
    }

    public function render()
    {
        $query = Requisicao::with(['user', 'livro.editora', 'livro.autores', 'recebidoPor']);

        // Se não for admin, mostrar apenas as requisições do utilizador
        if (!$this->isAdmin) {
            $query->where('user_id', Auth::id());
        }

        // Aplicar pesquisa
        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('livro', function ($livroQuery) {
                    $livroQuery->where('nome', 'like', '%' . $this->search . '%')
                              ->orWhere('isbn', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('user', function ($userQuery) {
                    $userQuery->where('name', 'like', '%' . $this->search . '%');
                });
            });
        }

        // Aplicar filtro de estado
        if ($this->filterEstado) {
            $query->where('estado', $this->filterEstado);
        }

        // Aplicar ordenação
        $query->orderBy($this->sortField, $this->sortDirection);

        $requisicoes = $query->paginate($this->perPage);

        // Calcular indicadores
        $statsQuery = Requisicao::query();
        if (!$this->isAdmin) {
            $statsQuery->where('user_id', Auth::id());
        }

        $requisicoesAtivas = (clone $statsQuery)
            ->where('estado', 'aprovada')
            ->whereNull('data_devolucao')
            ->count();

        $requisicoesUltimos30Dias = (clone $statsQuery)
            ->where('data_requisicao', '>=', now()->subDays(30))
            ->count();

        $livrosEntreguesHoje = (clone $statsQuery)
            ->whereDate('data_devolucao', today())
            ->count();

        return view('livewire.requisicoes-table', [
            'requisicoes' => $requisicoes,
            'requisicoesAtivas' => $requisicoesAtivas,
            'requisicoesUltimos30Dias' => $requisicoesUltimos30Dias,
            'livrosEntreguesHoje' => $livrosEntreguesHoje,
        ]);
    }
}
