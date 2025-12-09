<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-base-content leading-tight">
                Carrinho de Compras
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="alert alert-success mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if($carrinhoItems->isEmpty())
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 mx-auto text-base-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <h3 class="text-2xl font-bold mt-4">O seu carrinho está vazio</h3>
                        <p class="text-base-content/70">Adicione livros ao carrinho para começar a comprar.</p>
                        <div class="card-actions justify-center mt-4">
                            <a href="{{ route('livros.index') }}" class="btn btn-primary">Ver Livros</a>
                        </div>
                    </div>
                </div>
            @else
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Lista de Items -->
                    <div class="lg:col-span-2">
                        <div class="card bg-base-100 shadow-xl">
                            <div class="card-body">
                                <h3 class="card-title">Items no Carrinho ({{ $carrinhoItems->count() }})</h3>

                                <div class="space-y-4 mt-4">
                                    @foreach($carrinhoItems as $item)
                                        <div class="flex gap-4 p-4 border border-base-300 rounded-lg">
                                            <!-- Imagem do Livro -->
                                            <div class="flex-shrink-0">
                                                @if($item->livro->imagem_capa)
                                                    <img src="{{ $item->livro->imagem_capa }}" alt="{{ $item->livro->nome }}" class="w-24 h-32 object-cover rounded">
                                                @else
                                                    <div class="w-24 h-32 bg-base-300 rounded flex items-center justify-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-base-content/30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>

                                            <!-- Detalhes do Livro -->
                                            <div class="flex-grow">
                                                <h4 class="font-bold text-lg">{{ $item->livro->nome }}</h4>
                                                <p class="text-sm text-base-content/70">
                                                    {{ $item->livro->autores->pluck('nome')->join(', ') }}
                                                </p>
                                                <p class="text-sm text-base-content/70">
                                                    {{ $item->livro->editora->nome }}
                                                </p>
                                                <p class="text-lg font-bold text-primary mt-2">
                                                    €{{ number_format($item->livro->preco, 2) }}
                                                </p>

                                                <!-- Controles de Quantidade -->
                                                <div class="flex items-center gap-2 mt-3">
                                                    <form method="POST" action="{{ route('carrinho.update', $item) }}" class="flex items-center gap-2">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="number" name="quantidade" value="{{ $item->quantidade }}" min="1" class="input input-bordered input-sm w-20">
                                                        <button type="submit" class="btn btn-sm btn-ghost">Atualizar</button>
                                                    </form>

                                                    <form method="POST" action="{{ route('carrinho.destroy', $item) }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-error btn-outline">Remover</button>
                                                    </form>
                                                </div>
                                            </div>

                                            <!-- Subtotal -->
                                            <div class="text-right">
                                                <p class="font-bold text-lg">€{{ number_format($item->quantidade * $item->livro->preco, 2) }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Botão Limpar Carrinho -->
                                <div class="card-actions justify-end mt-4">
                                    <form method="POST" action="{{ route('carrinho.clear') }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-ghost" onclick="return confirm('Tem a certeza que deseja limpar o carrinho?')">
                                            Limpar Carrinho
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Resumo da Encomenda -->
                    <div class="lg:col-span-1">
                        <div class="card bg-base-100 shadow-xl sticky top-4">
                            <div class="card-body">
                                <h3 class="card-title">Resumo da Encomenda</h3>

                                <div class="space-y-2 mt-4">
                                    <div class="flex justify-between">
                                        <span>Subtotal</span>
                                        <span class="font-bold">€{{ number_format($subtotal, 2) }}</span>
                                    </div>

                                    <div class="flex justify-between">
                                        <span>Taxas</span>
                                        <span class="font-bold">€{{ number_format($taxas, 2) }}</span>
                                    </div>

                                    <div class="divider my-2"></div>

                                    <div class="flex justify-between text-lg font-bold">
                                        <span>Total</span>
                                        <span class="text-primary">€{{ number_format($total, 2) }}</span>
                                    </div>
                                </div>

                                <div class="card-actions mt-6">
                                    <a href="{{ route('checkout.shipping') }}" class="btn btn-primary btn-block">
                                        Proceder ao Checkout
                                    </a>
                                    <a href="{{ route('livros.index') }}" class="btn btn-ghost btn-block">
                                        Continuar a Comprar
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
