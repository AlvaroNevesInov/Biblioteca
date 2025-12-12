<?php

namespace App\Http\Controllers;

use App\Models\Encomenda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use Stripe\PaymentIntent;

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

    public function showPayment(Encomenda $encomenda)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Verificar se o utilizador pode ver esta encomenda
        if ($encomenda->user_id !== Auth::id()) {
            abort(403);
        }

        // Verificar se a encomenda está pendente
        if (!$encomenda->isPendente()) {
            return redirect()->route('encomendas.show', $encomenda)
                ->with('error', 'Esta encomenda não está pendente de pagamento.');
        }

        $encomenda->load('items.livro');

        // Configurar Stripe
        Stripe::setApiKey(config('services.stripe.secret'));

        // Criar Payment Intent
        $paymentIntent = PaymentIntent::create([
            'amount' => round($encomenda->total * 100), // Stripe usa centavos
            'currency' => 'eur',
            'metadata' => [
                'user_id' => Auth::id(),
                'encomenda_id' => $encomenda->id,
            ],
        ]);

        return view('encomendas.payment', compact('encomenda', 'paymentIntent'));
    }

    public function processPayment(Request $request, Encomenda $encomenda)
    {
        $request->validate([
            'payment_intent_id' => 'required|string'
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Verificar se o utilizador pode pagar esta encomenda
        if ($encomenda->user_id !== Auth::id()) {
            abort(403);
        }

        // Verificar se a encomenda está pendente
        if (!$encomenda->isPendente()) {
            return redirect()->route('encomendas.show', $encomenda)
                ->with('error', 'Esta encomenda não está pendente de pagamento.');
        }

        try {
            DB::beginTransaction();

            // Atualizar encomenda com pagamento
            $encomenda->update([
                'estado' => 'paga',
                'stripe_payment_intent_id' => $request->payment_intent_id,
            ]);

            DB::commit();

            return response(route('encomendas.show', $encomenda->id));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Ocorreu um erro ao processar o pagamento. Por favor, tente novamente.');
        }
    }
}
