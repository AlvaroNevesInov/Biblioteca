<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Requisicao;
use App\Models\User;
use App\Mail\NovoReviewAdmin;
use App\Mail\StatusReviewCidadao;
use App\Services\LogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ReviewController extends Controller
{
    /**
     * Display a listing of reviews (Admin vê todos, apenas suspensos por padrão).
     */
    public function index()
    {
        return view('reviews.index');
    }

    /**
     * Store a newly created review in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'requisicao_id' => 'required|exists:requisicoes,id',
            'comentario' => 'required|string|min:10|max:1000',
        ]);

        $requisicao = Requisicao::with(['livro', 'user'])->findOrFail($validated['requisicao_id']);

        // Verificar se a requisição pertence ao utilizador autenticado
        if ($requisicao->user_id !== Auth::id()) {
            abort(403, 'Não tem permissão para criar review desta requisição.');
        }

        // Verificar se a requisição pode receber review
        if (!$requisicao->podeReceberReview()) {
            return redirect()->back()
                ->with('error', 'Não é possível criar review para esta requisição. A requisição deve estar devolvida e não pode já ter um review.');
        }

        // Criar o review
        $review = Review::create([
            'user_id' => Auth::id(),
            'livro_id' => $requisicao->livro_id,
            'requisicao_id' => $requisicao->id,
            'comentario' => $validated['comentario'],
            'estado' => 'suspenso',
        ]);

        // Carregar relações para os emails
        $review->load(['livro', 'user', 'requisicao']);

        // Enviar email para todos os administradores com delays maiores
        $admins = User::where('role', 'admin')->get();

        foreach ($admins as $index => $admin) {
            Mail::to($admin->email)
                ->later(now()->addSeconds(10 + ($index * 20)), new NovoReviewAdmin($review));
        }

        LogService::logCreate(
            'Reviews',
            $review->id,
            "Novo review criado para o livro '{$requisicao->livro->nome}' pelo utilizador {$review->user->name}"
        );

        return redirect()->route('requisicoes.index')
            ->with('success', 'Review submetido com sucesso! Aguarde a aprovação do administrador.');
    }

    /**
     * Display the specified review (Admin apenas).
     */
    public function show(Review $review)
    {
        // Carregar relações necessárias
        $review->load(['user', 'livro', 'requisicao']);

        return view('reviews.show', compact('review'));
    }

    /**
     * Aprovar um review (Admin apenas).
     */
    public function aprovar(Review $review)
    {
        if (!$review->isSuspenso()) {
            return redirect()->back()
                ->with('error', 'Apenas reviews suspensos podem ser aprovados.');
        }

        $review->update([
            'estado' => 'ativo',
            'justificacao_recusa' => null,
        ]);

        // Enviar email ao cidadão com delay maior
        Mail::to($review->user->email)
            ->later(now()->addSeconds(15), new StatusReviewCidadao($review));

        LogService::log(
            'Reviews',
            'Aprovar',
            $review->id,
            "Review #{$review->id} aprovado - Livro: '{$review->livro->nome}', Utilizador: {$review->user->name}"
        );

        return redirect()->route('reviews.index')
            ->with('success', 'Review aprovado com sucesso!');
    }

    /**
     * Recusar um review (Admin apenas).
     */
    public function recusar(Request $request, Review $review)
    {
        $validated = $request->validate([
            'justificacao_recusa' => 'required|string|min:10|max:500',
        ]);

        if (!$review->isSuspenso()) {
            return redirect()->back()
                ->with('error', 'Apenas reviews suspensos podem ser recusados.');
        }

        $review->update([
            'estado' => 'recusado',
            'justificacao_recusa' => $validated['justificacao_recusa'],
        ]);

        // Enviar email ao cidadão com delay maior
        Mail::to($review->user->email)
            ->later(now()->addSeconds(15), new StatusReviewCidadao($review));

        LogService::log(
            'Reviews',
            'Recusar',
            $review->id,
            "Review #{$review->id} recusado - Livro: '{$review->livro->nome}', Utilizador: {$review->user->name}"
        );

        return redirect()->route('reviews.index')
            ->with('success', 'Review recusado com sucesso!');
    }

    /**
     * Remove the specified review from storage (Admin apenas).
     */
    public function destroy(Review $review)
    {
        $livroNome = $review->livro->nome;
        $userName = $review->user->name;
        $reviewId = $review->id;

        $review->delete();

        LogService::logDelete(
            'Reviews',
            $reviewId,
            "Review #{$reviewId} eliminado - Livro: '{$livroNome}', Utilizador: {$userName}"
        );

        return redirect()->route('reviews.index')
            ->with('success', 'Review eliminado com sucesso!');
    }
}
