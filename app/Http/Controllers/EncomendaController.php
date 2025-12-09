<?php

namespace App\Http\Controllers;

use App\Models\Encomenda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EncomendaController extends Controller
{

    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Admin vê todas as encomendas, cidadãos vêem apenas as suas
        if ($user->isAdmin()) {
            $encomendas = Encomenda::with('user', 'items.livro')
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        } else {
            $encomendas = $user->encomendas()
                ->with('items.livro')
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        }

        return view('encomendas.index', compact('encomendas'));
    }

    public function show(Encomenda $encomenda)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Verificar se o utilizador pode ver esta encomenda
        if (!$user->isAdmin() && $encomenda->user_id !== Auth::id()) {
            abort(403);
        }

        $encomenda->load('user', 'items.livro.editora', 'items.livro.autores');

        return view('encomendas.show', compact('encomenda'));
    }

    public function updateStatus(Request $request, Encomenda $encomenda)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Apenas admin pode atualizar o estado
        if (!$user->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'estado' => 'required|in:pendente,paga,processando,enviada,entregue,cancelada'
        ]);

        $encomenda->update([
            'estado' => $request->estado
        ]);

        return redirect()->back()->with('success', 'Estado da encomenda atualizado com sucesso!');
    }
}
