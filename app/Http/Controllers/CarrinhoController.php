<?php

namespace App\Http\Controllers;

use App\Models\CarrinhoItem;
use App\Models\Livro;
use App\Services\LogService;
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
            $oldQuantidade = $carrinhoItem->quantidade;
            $carrinhoItem->update([
                'quantidade' => $carrinhoItem->quantidade + ($request->quantidade ?? 1)
            ]);

            LogService::log(
                'Carrinho',
                'Atualizar Quantidade',
                $carrinhoItem->id,
                "Quantidade do livro '{$livro->nome}' atualizada no carrinho ({$oldQuantidade} → {$carrinhoItem->quantidade})"
            );
        } else {
            // Criar novo item no carrinho
            $carrinhoItem = CarrinhoItem::create([
                'user_id' => Auth::id(),
                'livro_id' => $livro->id,
                'quantidade' => $request->quantidade ?? 1
            ]);

            LogService::log(
                'Carrinho',
                'Adicionar Item',
                $carrinhoItem->id,
                "Livro '{$livro->nome}' adicionado ao carrinho (quantidade: {$carrinhoItem->quantidade})"
            );
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

        $oldQuantidade = $carrinhoItem->quantidade;
        $carrinhoItem->update([
            'quantidade' => $request->quantidade
        ]);

        LogService::log(
            'Carrinho',
            'Atualizar Quantidade',
            $carrinhoItem->id,
            "Quantidade do livro '{$carrinhoItem->livro->nome}' alterada ({$oldQuantidade} → {$carrinhoItem->quantidade})"
        );

        return redirect()->route('carrinho.index')->with('success', 'Quantidade atualizada com sucesso!');
    }

    public function destroy(CarrinhoItem $carrinhoItem)
    {
        // Verificar se o item pertence ao utilizador
        if ($carrinhoItem->user_id !== Auth::id()) {
            abort(403);
        }

        $livroNome = $carrinhoItem->livro->nome;
        $carrinhoItemId = $carrinhoItem->id;

        $carrinhoItem->delete();

        LogService::log(
            'Carrinho',
            'Remover Item',
            $carrinhoItemId,
            "Livro '{$livroNome}' removido do carrinho"
        );

        return redirect()->route('carrinho.index')->with('success', 'Item removido do carrinho!');
    }

    public function clear()
    {

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $totalItems = $user->carrinhoItems()->count();
        $user->carrinhoItems()->delete();

        LogService::log(
            'Carrinho',
            'Limpar Carrinho',
            null,
            "Carrinho limpo - {$totalItems} itens removidos"
        );

        return redirect()->route('carrinho.index')->with('success', 'Carrinho limpo com sucesso!');
    }
}
