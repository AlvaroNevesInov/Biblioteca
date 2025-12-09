<?php

namespace App\Http\Controllers;

use App\Models\Encomenda;
use App\Models\EncomendaItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use Stripe\PaymentIntent;

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
        ]);

        // Guardar dados de envio na sessão
        session(['shipping_data' => $validated]);

        return redirect()->route('checkout.payment');
    }

    public function showPayment()
    {
        if (!session('shipping_data')) {
            return redirect()->route('checkout.shipping');
        }
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $carrinhoItems = $user->carrinhoItems()->with('livro')->get();

        if ($carrinhoItems->isEmpty()) {
            return redirect()->route('carrinho.index')->with('error', 'O seu carrinho está vazio!');
        }

        $subtotal = $carrinhoItems->sum(function ($item) {
            return $item->quantidade * $item->livro->preco;
        });

        $taxas = 0;
        $total = $subtotal + $taxas;

        $shippingData = session('shipping_data');

        // Configurar Stripe
        Stripe::setApiKey(config('services.stripe.secret'));

        // Criar Payment Intent
        $paymentIntent = PaymentIntent::create([
            'amount' => round($total * 100), // Stripe usa centavos
            'currency' => 'eur',
            'metadata' => [
                'user_id' => Auth::id(),
            ],
        ]);

        return view('checkout.payment', compact(
            'carrinhoItems',
            'subtotal',
            'taxas',
            'total',
            'shippingData',
            'paymentIntent'
        ));
    }

    public function processPayment(Request $request)
    {
        $request->validate([
            'payment_intent_id' => 'required|string'
        ]);

        if (!session('shipping_data')) {
            return redirect()->route('checkout.shipping');
        }

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
                'stripe_payment_intent_id' => $request->payment_intent_id,
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

            /** @var \App\Models\User $user */
            $user = Auth::user();

            // Limpar carrinho
            $user->carrinhoItems()->delete();

            // Limpar sessão
            session()->forget('shipping_data');

            DB::commit();

            return response(route('checkout.success', $encomenda->id));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Ocorreu um erro ao processar o pagamento. Por favor, tente novamente.');
        }
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
