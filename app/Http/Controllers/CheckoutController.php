<?php

namespace App\Http\Controllers;

use App\Models\Encomenda;
use App\Models\EncomendaItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Checkout\Session as StripeSession;

class CheckoutController extends Controller
{

    public function showShipping()
    {

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $carrinhoItems = $user->carrinhoItems()->with('livro')->get();

        if ($carrinhoItems->isEmpty()) {
            return redirect()->route('carrinho.index')->with('error', 'O seu carrinho está vazio!');
        }

        $subtotal = $carrinhoItems->sum(function ($item) {
            return $item->quantidade * $item->livro->preco;
        });

        return view('checkout.shipping', compact('carrinhoItems', 'subtotal'));
    }

    public function processShipping(Request $request)
    {
        $validated = $request->validate([
            'nome_completo' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'telefone' => 'nullable|string|max:20',
            'morada' => 'required|string',
            'cidade' => 'required|string|max:255',
            'codigo_postal' => 'required|string|max:20',
            'pais' => 'required|string|max:255',
            'payment_action' => 'required|in:pay_now,pay_later',
        ]);

        // Se o utilizador quer pagar depois, criar encomenda pendente
        if ($validated['payment_action'] === 'pay_later') {
            return $this->createPendingOrder($request);
        }

        // Guardar dados de envio na sessão
        session(['shipping_data' => $validated]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $carrinhoItems = $user->carrinhoItems()->with('livro')->get();

        if ($carrinhoItems->isEmpty()) {
            return redirect()->route('carrinho.index')->with('error', 'O seu carrinho está vazio!');
        }

        // Configurar Stripe
        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            // Preparar line items para o Stripe Checkout
            $lineItems = [];
            foreach ($carrinhoItems as $item) {
                $lineItems[] = [
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => $item->livro->nome,
                            'description' => 'ISBN: ' . ($item->livro->isbn ?? 'N/A'),
                        ],
                        'unit_amount' => round($item->livro->preco * 100), // Stripe usa centavos
                    ],
                    'quantity' => $item->quantidade,
                ];
            }

            // Criar Stripe Checkout Session
            $checkoutSession = StripeSession::create([
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => route('checkout.stripe.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('checkout.stripe.cancel'),
                'customer_email' => $validated['email'],
                'metadata' => [
                    'user_id' => Auth::id(),
                    'user_role' => $user->role,
                ],
            ]);

            Log::info('Stripe Checkout Session criada', [
                'session_id' => $checkoutSession->id,
                'user_id' => Auth::id(),
                'user_role' => $user->role,
            ]);

            // Redirecionar para a página do Stripe
            return redirect($checkoutSession->url);

        } catch (\Exception $e) {
            Log::error('Erro ao criar Stripe Checkout Session', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);
            return redirect()->back()->with('error', 'Erro ao inicializar o pagamento. Por favor, tente novamente.');
        }
    }

    public function createPendingOrder(Request $request)
    {
        // Validar dados de envio
        $validated = $request->validate([
            'nome_completo' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'telefone' => 'nullable|string|max:20',
            'morada' => 'required|string',
            'cidade' => 'required|string|max:255',
            'codigo_postal' => 'required|string|max:20',
            'pais' => 'required|string|max:255',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $carrinhoItems = $user->carrinhoItems()->with('livro')->get();

        if ($carrinhoItems->isEmpty()) {
            return redirect()->route('carrinho.index')->with('error', 'O seu carrinho está vazio!');
        }

        try {
            DB::beginTransaction();

            $subtotal = $carrinhoItems->sum(function ($item) {
                return $item->quantidade * $item->livro->preco;
            });

            $taxas = 0;
            $total = $subtotal + $taxas;

            // Criar encomenda com estado pendente
            $encomenda = Encomenda::create([
                'user_id' => Auth::id(),
                'nome_completo' => $validated['nome_completo'],
                'email' => $validated['email'],
                'telefone' => $validated['telefone'] ?? null,
                'morada' => $validated['morada'],
                'cidade' => $validated['cidade'],
                'codigo_postal' => $validated['codigo_postal'],
                'pais' => $validated['pais'],
                'subtotal' => $subtotal,
                'taxas' => $taxas,
                'total' => $total,
                'estado' => 'pendente',
                'stripe_payment_intent_id' => null,
            ]);

            // Criar items da encomenda
            foreach ($carrinhoItems as $item) {
                EncomendaItem::create([
                    'encomenda_id' => $encomenda->id,
                    'livro_id' => $item->livro_id,
                    'quantidade' => $item->quantidade,
                    'preco_unitario' => $item->livro->preco,
                    'subtotal' => $item->quantidade * $item->livro->preco,
                ]);
            }

            // Limpar carrinho
            $user->carrinhoItems()->delete();

            DB::commit();

            Log::info('Encomenda pendente criada com sucesso', [
                'encomenda_id' => $encomenda->id,
                'user_id' => Auth::id(),
            ]);

            return redirect()->route('encomendas.show', $encomenda->id)
                ->with('success', 'Encomenda criada com sucesso! Pode efetuar o pagamento quando desejar.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao criar encomenda pendente: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Ocorreu um erro ao criar a encomenda. Por favor, tente novamente.');
        }
    }

    public function stripeSuccess(Request $request)
    {
        $sessionId = $request->query('session_id');

        if (!$sessionId) {
            return redirect()->route('carrinho.index')->with('error', 'Sessão de pagamento inválida.');
        }

        if (!session('shipping_data')) {
            return redirect()->route('carrinho.index')->with('error', 'Dados de envio não encontrados.');
        }

        // Configurar Stripe
        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            // Recuperar a sessão do Stripe
            $stripeSession = StripeSession::retrieve($sessionId);

            if ($stripeSession->payment_status !== 'paid') {
                return redirect()->route('checkout.shipping')->with('error', 'Pagamento não foi confirmado.');
            }

            /** @var \App\Models\User $user */
            $user = Auth::user();
            $carrinhoItems = $user->carrinhoItems()->with('livro')->get();

            if ($carrinhoItems->isEmpty()) {
                return redirect()->route('carrinho.index')->with('error', 'O seu carrinho está vazio!');
            }

            DB::beginTransaction();

            $subtotal = $carrinhoItems->sum(function ($item) {
                return $item->quantidade * $item->livro->preco;
            });

            $taxas = 0;
            $total = $subtotal + $taxas;

            $shippingData = session('shipping_data');

            // Criar encomenda
            $encomenda = Encomenda::create([
                'user_id' => Auth::id(),
                'nome_completo' => $shippingData['nome_completo'],
                'email' => $shippingData['email'],
                'telefone' => $shippingData['telefone'] ?? null,
                'morada' => $shippingData['morada'],
                'cidade' => $shippingData['cidade'],
                'codigo_postal' => $shippingData['codigo_postal'],
                'pais' => $shippingData['pais'],
                'subtotal' => $subtotal,
                'taxas' => $taxas,
                'total' => $total,
                'estado' => 'paga',
                'stripe_payment_intent_id' => $stripeSession->payment_intent,
            ]);

            // Criar items da encomenda
            foreach ($carrinhoItems as $item) {
                EncomendaItem::create([
                    'encomenda_id' => $encomenda->id,
                    'livro_id' => $item->livro_id,
                    'quantidade' => $item->quantidade,
                    'preco_unitario' => $item->livro->preco,
                    'subtotal' => $item->quantidade * $item->livro->preco,
                ]);
            }

            // Limpar carrinho
            $user->carrinhoItems()->delete();

            // Limpar sessão
            session()->forget('shipping_data');

            DB::commit();

            Log::info('Encomenda criada com sucesso após pagamento Stripe', [
                'encomenda_id' => $encomenda->id,
                'user_id' => Auth::id(),
                'session_id' => $sessionId
            ]);

            return redirect()->route('checkout.success', $encomenda->id);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao processar retorno do Stripe: ' . $e->getMessage(), [
                'session_id' => $sessionId,
                'user_id' => Auth::id()
            ]);
            return redirect()->route('carrinho.index')->with('error', 'Ocorreu um erro ao processar o pagamento. Por favor, contacte o suporte.');
        }
    }

    public function stripeCancel()
    {
        Log::info('Pagamento cancelado pelo usuário', [
            'user_id' => Auth::id()
        ]);

        return redirect()->route('checkout.shipping')->with('error', 'Pagamento cancelado. Pode tentar novamente quando quiser.');
    }

    public function success($encomendaId)
    {
        $encomenda = Encomenda::with('items.livro')->findOrFail($encomendaId);

        // Verificar se a encomenda pertence ao utilizador
        if ($encomenda->user_id !== Auth::id()) {
            abort(403);
        }

        return view('checkout.success', compact('encomenda'));
    }
}
