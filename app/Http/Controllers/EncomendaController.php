<?php

namespace App\Http\Controllers;

use App\Models\Encomenda;
use App\Services\LogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

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

        $oldEstado = $encomenda->estado;
        $encomenda->update([
            'estado' => $request->estado
        ]);

        LogService::log(
            'Encomendas',
            'Atualizar Estado',
            $encomenda->id,
            "Estado da encomenda #{$encomenda->numero_encomenda} alterado de '{$oldEstado}' para '{$request->estado}'"
        );

        return redirect()->back()->with('success', 'Estado da encomenda atualizado com sucesso!');
    }

    public function payPendingOrder(Encomenda $encomenda)

    {

        // Verificar se o utilizador pode pagar esta encomenda

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



        try {

            // Preparar line items para o Stripe Checkout

            $lineItems = [];

            foreach ($encomenda->items as $item) {

                $lineItems[] = [

                    'price_data' => [

                        'currency' => 'eur',

                        'product_data' => [

                            'name' => $item->livro->nome,

                            'description' => 'ISBN: ' . ($item->livro->isbn ?? 'N/A'),

                        ],

                        'unit_amount' => round($item->preco_unitario * 100), // Stripe usa centavos

                    ],

                    'quantity' => $item->quantidade,

                ];

            }



            // Criar Stripe Checkout Session

            $checkoutSession = StripeSession::create([

                'payment_method_types' => ['card'],

                'line_items' => $lineItems,

                'mode' => 'payment',

                'success_url' => route('encomendas.stripe.success', $encomenda->id) . '?session_id={CHECKOUT_SESSION_ID}',

                'cancel_url' => route('encomendas.show', $encomenda->id),

                'customer_email' => $encomenda->email,

                'metadata' => [

                    'user_id' => Auth::id(),

                    'encomenda_id' => $encomenda->id,

                ],

            ]);



            Log::info('Stripe Checkout Session criada para encomenda pendente', [

                'session_id' => $checkoutSession->id,

                'encomenda_id' => $encomenda->id,

                'user_id' => Auth::id(),

            ]);

            LogService::log(
                'Encomendas',
                'Iniciar Pagamento',
                $encomenda->id,
                "Pagamento iniciado para encomenda pendente #{$encomenda->numero_encomenda} (Valor: €{$encomenda->total})"
            );



            // Redirecionar para a página do Stripe

            return redirect($checkoutSession->url);



        } catch (\Exception $e) {

            Log::error('Erro ao criar Stripe Checkout Session para encomenda pendente', [

                'error' => $e->getMessage(),

                'encomenda_id' => $encomenda->id,

                'user_id' => Auth::id(),

            ]);

            return redirect()->back()->with('error', 'Erro ao inicializar o pagamento. Por favor, tente novamente.');

        }

    }



    public function stripeSuccess(Request $request, Encomenda $encomenda)

    {

        $sessionId = $request->query('session_id');



        if (!$sessionId) {

            return redirect()->route('encomendas.show', $encomenda->id)

                ->with('error', 'Sessão de pagamento inválida.');

        }



        // Verificar se o utilizador pode pagar esta encomenda

        if ($encomenda->user_id !== Auth::id()) {

            abort(403);

        }



        // Verificar se a encomenda está pendente

        if (!$encomenda->isPendente()) {

            return redirect()->route('encomendas.show', $encomenda)

                ->with('info', 'Esta encomenda já foi paga.');

        }



        // Configurar Stripe

        Stripe::setApiKey(config('services.stripe.secret'));



        try {

            // Recuperar a sessão do Stripe

            $stripeSession = StripeSession::retrieve($sessionId);



            if ($stripeSession->payment_status !== 'paid') {

                return redirect()->route('encomendas.show', $encomenda->id)

                    ->with('error', 'Pagamento não foi confirmado.');

            }



            DB::beginTransaction();



            // Atualizar encomenda

            $encomenda->update([

                'estado' => 'paga',

                'stripe_payment_intent_id' => $stripeSession->payment_intent,

            ]);

            LogService::log(
                'Encomendas',
                'Pagamento Concluído',
                $encomenda->id,
                "Pagamento concluído para encomenda #{$encomenda->numero_encomenda} (Valor: €{$encomenda->total})"
            );



            DB::commit();



            Log::info('Encomenda pendente paga com sucesso', [

                'encomenda_id' => $encomenda->id,

                'user_id' => Auth::id(),

                'session_id' => $sessionId

            ]);



            return redirect()->route('encomendas.show', $encomenda->id)

                ->with('success', 'Pagamento realizado com sucesso!');



        } catch (\Exception $e) {

            DB::rollBack();

            Log::error('Erro ao processar retorno do Stripe para encomenda pendente', [

                'error' => $e->getMessage(),

                'session_id' => $sessionId,

                'encomenda_id' => $encomenda->id,

                'user_id' => Auth::id()

            ]);

            return redirect()->route('encomendas.show', $encomenda->id)

                ->with('error', 'Ocorreu um erro ao processar o pagamento. Por favor, contacte o suporte.');



        }
    }
}
