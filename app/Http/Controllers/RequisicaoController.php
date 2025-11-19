<?php

namespace App\Http\Controllers;

use App\Models\Requisicao;
use App\Models\Livro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RequisicaoController extends Controller
{
    /**
     * Display a listing of requisições.
     */
    public function index()
    {
        return view('requisicoes.index');
    }

    /**
     * Show the form for creating a new requisição.
     */
    public function create(Request $request)
    {
        $user = Auth::user();

        // Verificar se o cidadão pode requisitar mais livros (máximo 3)
        if (!$user->podeRequisitar()) {
            return redirect()->route('requisicoes.index')
                ->with('error', 'Já atingiu o limite máximo de 3 livros requisitados em simultâneo. Devolva um livro para poder requisitar outro.');
        }

        $livro = null;
        if ($request->has('livro_id')) {
            $livro = Livro::with(['editora', 'autores'])->findOrFail($request->livro_id);

            if (!$livro->estaDisponivel()) {
                return redirect()->route('livros.index')
                    ->with('error', 'Este livro já está requisitado e não está disponível no momento.');
            }
        }

        $livros = Livro::with(['editora', 'autores'])
            ->orderBy('nome')
            ->get()
            ->filter(fn($livro) => $livro->estaDisponivel());

        return view('requisicoes.create', compact('livros', 'livro'));
    }

    /**
     * Store a newly created requisição in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'livro_id' => 'required|exists:livros,id',
            'foto_cidadao' => 'required|image|max:2048',
            'observacoes' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();

        // Verificar se o cidadão pode requisitar mais livros (máximo 3)
        if (!$user->podeRequisitar()) {
            return redirect()->route('requisicoes.index')
                ->with('error', 'Já atingiu o limite máximo de 3 livros requisitados em simultâneo.');
        }

        $livro = Livro::findOrFail($validated['livro_id']);

        if (!$livro->estaDisponivel()) {
            return redirect()->route('requisicoes.create')
                ->with('error', 'Este livro já não está disponível para requisição.');
        }

        $requisicaoExistente = Requisicao::where('user_id', Auth::id())
            ->where('livro_id', $validated['livro_id'])
            ->whereIn('estado', ['pendente', 'aprovada'])
            ->exists();

        if ($requisicaoExistente) {
            return redirect()->route('requisicoes.index')
                ->with('error', 'Já tem uma requisição ativa deste livro.');
        }

        // Upload da foto do cidadão (obrigatório - já validado)
        $file = $request->file('foto_cidadao');
        $filename = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
        $uploadPath = public_path('uploads/requisicoes');

        // Criar pasta se não existir
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $file->move($uploadPath, $filename);
        $fotoCidadao = '/uploads/requisicoes/' . $filename;

        Requisicao::create([
            'user_id' => Auth::id(),
            'livro_id' => $validated['livro_id'],
            'foto_cidadao' => $fotoCidadao,
            'estado' => 'pendente',
            'data_requisicao' => Carbon::today(),
            'data_prevista_devolucao' => Carbon::today()->addDays(5),
            'observacoes' => $validated['observacoes'] ?? null,
        ]);

        return redirect()->route('requisicoes.index')
            ->with('success', 'Requisição criada com sucesso! Aguarde aprovação do administrador.');
    }

    /**
     * Show the form for editing the specified requisição (apenas admin).
     */
    public function edit(Requisicao $requisicao)
    {
        if (!optional(Auth::user())->isAdmin()) {
            abort(403, 'Acesso negado.');
        }

        $livros = Livro::with(['editora', 'autores'])->orderBy('nome')->get();

        return view('requisicoes.edit', compact('requisicao', 'livros'));
    }

    /**
     * Update the specified requisição in storage (apenas admin).
     */
    public function update(Request $request, Requisicao $requisicao)
    {
        if (!optional(Auth::user())->isAdmin()) {
            abort(403, 'Acesso negado.');
        }

        $validated = $request->validate([
            'estado' => 'required|in:pendente,aprovada,rejeitada,devolvida',
            'data_prevista_devolucao' => 'nullable|date|after_or_equal:data_requisicao',
            'data_devolucao' => 'nullable|date|after_or_equal:data_requisicao',
            'observacoes' => 'nullable|string|max:500',
        ]);

        $requisicao->update($validated);

        return redirect()->route('requisicoes.index')
            ->with('success', 'Requisição atualizada com sucesso!');
    }

    /**
     * Aprovar uma requisição (apenas admin).
     */
    public function aprovar(Requisicao $requisicao)
    {
        if (!optional(Auth::user())->isAdmin()) {
            abort(403, 'Acesso negado.');
        }

        if (!$requisicao->livro->estaDisponivel() && $requisicao->estado !== 'pendente') {
            return redirect()->route('requisicoes.index')
                ->with('error', 'Este livro já não está disponível.');
        }

        $requisicao->update(['estado' => 'aprovada']);

        return redirect()->route('requisicoes.index')
            ->with('success', 'Requisição aprovada com sucesso!');
    }

    /**
     * Rejeitar uma requisição (apenas admin).
     */
    public function rejeitar(Request $request, Requisicao $requisicao)
    {
        if (!optional(Auth::user())->isAdmin()) {
            abort(403, 'Acesso negado.');
        }

        $validated = $request->validate(['observacoes' => 'nullable|string|max:500']);

        $requisicao->update([
            'estado' => 'rejeitada',
            'observacoes' => $validated['observacoes'] ?? $requisicao->observacoes,
        ]);

        return redirect()->route('requisicoes.index')
            ->with('success', 'Requisição rejeitada.');
    }

    /**
     * Marcar como devolvida (apenas admin).
     */
    public function devolver(Requisicao $requisicao)
    {
        if (!optional(Auth::user())->isAdmin()) {
            abort(403, 'Acesso negado.');
        }

        $requisicao->update([
            'estado' => 'devolvida',
            'data_devolucao' => Carbon::today(),
        ]);

        return redirect()->route('requisicoes.index')
            ->with('success', 'Livro marcado como devolvido!');
    }

    /**
     * Remove the specified requisição from storage (apenas criador ou admin).
     */
    public function destroy(Requisicao $requisicao)
    {
        $user = Auth::user();

        if (!optional($user)->isAdmin() && $requisicao->user_id !== Auth::id()) {
            abort(403, 'Acesso negado.');
        }

        if (!$requisicao->isPendente()) {
            return redirect()->route('requisicoes.index')
                ->with('error', 'Apenas requisições pendentes podem ser canceladas.');
        }

        $requisicao->delete();

        return redirect()->route('requisicoes.index')
            ->with('success', 'Requisição cancelada com sucesso!');
    }
}
