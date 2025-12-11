<x-mail::message>
# Olá {{ $user->name }},

Reparámos que deixou alguns livros no seu carrinho há algum tempo.

## Precisa de ajuda?

Estamos aqui para ajudar! Se tiver alguma dúvida sobre os livros ou sobre o processo de compra, não hesite em contactar-nos.

### Os seus itens:

@foreach($carrinhoItems as $item)
- **{{ $item->livro->nome }}** - {{ $item->quantidade }}x {{ number_format($item->livro->preco, 2, ',', '.') }}€
@endforeach

**Total: {{ number_format($total, 2, ',', '.') }}€**

<x-mail::button :url="url('/carrinho')">
Ver o Meu Carrinho
</x-mail::button>

Ficamos à sua disposição para qualquer esclarecimento.

Obrigado,<br>
Equipa {{ config('app.name') }}
</x-mail::message>
