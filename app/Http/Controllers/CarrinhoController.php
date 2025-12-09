<?php

namespace App\Http\Controllers;

use App\Models\CarrinhoItem;
use App\Models\Livro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CarrinhoController extends Controller
{

    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $carrinhoItems = $user->carrinhoItems()
            ->with('livro.editora', 'livro.autores')
            ->get();

        $subtotal = $carrinhoItems->sum(function ($item) {
            return $item->quantidade * $item->livro->preco;
        });

        $taxas = 0;
        $total = $subtotal + $taxas;

        return view('carrinho.index', compact('carrinhoItems', 'subtotal', 'taxas', 'total'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'livro_id' => 'required|exists:livros,id',
            'quantidade' => 'integer|min:1'
        ]);

        $livro = Livro::findOrFail($request->livro_id);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Verificar se o item já existe no carrinho
        $carrinhoItem = $user->carrinhoItems()
            ->where('livro_id', $livro->id)
            ->first();

        if ($carrinhoItem) {
            // Atualizar quantidade
            $carrinhoItem->update([
                'quantidade' => $carrinhoItem->quantidade + ($request->quantidade ?? 1)
            ]);
        } else {
            // Criar novo item no carrinho
            CarrinhoItem::create([
                'user_id' => Auth::id(),
                'livro_id' => $livro->id,
                'quantidade' => $request->quantidade ?? 1
            ]);
        }

        return redirect()->back()->with('success', 'Livro adicionado ao carrinho com sucesso!');
    }

    public function update(Request $request, CarrinhoItem $carrinhoItem)
    {
        // Verificar se o item pertence ao utilizador
        if ($carrinhoItem->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'quantidade' => 'required|integer|min:1'
        ]);

        $carrinhoItem->update([
            'quantidade' => $request->quantidade
        ]);

        return redirect()->route('carrinho.index')->with('success', 'Quantidade atualizada com sucesso!');
    }

    public function destroy(CarrinhoItem $carrinhoItem)
    {
        // Verificar se o item pertence ao utilizador
        if ($carrinhoItem->user_id !== Auth::id()) {
            abort(403);
        }

        $carrinhoItem->delete();

        return redirect()->route('carrinho.index')->with('success', 'Item removido do carrinho!');
    }

    public function clear()
    {

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $user->carrinhoItems()->delete();

        return redirect()->route('carrinho.index')->with('success', 'Carrinho limpo com sucesso!');
    }
}
