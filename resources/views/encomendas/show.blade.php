<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-base-content leading-tight">
                Encomenda #{{ $encomenda->numero_encomenda }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="alert alert-success mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- Voltar -->
            <div class="mb-6">
                <a href="{{ route('encomendas.index') }}" class="btn btn-ghost btn-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Voltar
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Informações da Encomenda -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Detalhes Gerais -->
                    <div class="card bg-base-100 shadow-xl">
                        <div class="card-body">
                            <h3 class="card-title text-2xl">Detalhes da Encomenda</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                <div>
                                    <p class="text-sm text-base-content/70">Número da Encomenda</p>
                                    <p class="font-bold text-lg font-mono">{{ $encomenda->numero_encomenda }}</p>
                                </div>

                                <div>
                                    <p class="text-sm text-base-content/70">Data</p>
                                    <p class="font-bold">{{ $encomenda->created_at->format('d/m/Y H:i') }}</p>
                                </div>

                                @if(auth()->user()->isAdmin())
                                    <div>
                                        <p class="text-sm text-base-content/70">Cliente</p>
                                        <p class="font-bold">{{ $encomenda->user->name }}</p>
                                        <p class="text-sm text-base-content/70">{{ $encomenda->user->email }}</p>
                                    </div>
                                @endif

                                <div>
                                    <p class="text-sm text-base-content/70">Total</p>
                                    <p class="font-bold text-primary text-lg">€{{ number_format($encomenda->total, 2) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Morada de Entrega -->
                    <div class="card bg-base-100 shadow-xl">
                        <div class="card-body">
                            <h3 class="card-title">Morada de Entrega</h3>
                            <div class="text-sm mt-2">
                                <p><strong>{{ $encomenda->nome_completo }}</strong></p>
                                <p>{{ $encomenda->email }}</p>
                                @if($encomenda->telefone)
                                    <p>{{ $encomenda->telefone }}</p>
                                @endif
                                <p class="mt-2">{{ $encomenda->morada }}</p>
                                <p>{{ $encomenda->cidade }}, {{ $encomenda->codigo_postal }}</p>
                                <p>{{ $encomenda->pais }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Items da Encomenda -->
                    <div class="card bg-base-100 shadow-xl">
                        <div class="card-body">
                            <h3 class="card-title">Items da Encomenda</h3>

                            <div class="space-y-4 mt-4">
                                @foreach($encomenda->items as $item)
                                    <div class="flex gap-4 p-4 border border-base-300 rounded-lg">
                                        <!-- Imagem do Livro -->
                                        <div class="flex-shrink-0">
                                            @if($item->livro->imagem_capa)
                                                <img src="{{ $item->livro->imagem_capa }}" alt="{{ $item->livro->nome }}" class="w-20 h-28 object-cover rounded">
                                            @else
                                                <div class="w-20 h-28 bg-base-300 rounded flex items-center justify-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-base-content/30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
                                            <div class="mt-2">
                                                <span class="text-sm">Quantidade: <strong>{{ $item->quantidade }}</strong></span>
                                                <span class="mx-2">•</span>
                                                <span class="text-sm">Preço: <strong>€{{ number_format($item->preco_unitario, 2) }}</strong></span>
                                            </div>
                                        </div>

                                        <!-- Subtotal -->
                                        <div class="text-right">
                                            <p class="font-bold text-lg">€{{ number_format($item->subtotal, 2) }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Totais -->
                            <div class="divider"></div>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span>Subtotal</span>
                                    <span class="font-bold">€{{ number_format($encomenda->subtotal, 2) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Taxas</span>
                                    <span class="font-bold">€{{ number_format($encomenda->taxas, 2) }}</span>
                                </div>
                                <div class="divider my-2"></div>
                                <div class="flex justify-between text-lg font-bold">
                                    <span>Total</span>
                                    <span class="text-primary">€{{ number_format($encomenda->total, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar - Estado e Ações -->
                <div class="lg:col-span-1">
                    <div class="card bg-base-100 shadow-xl sticky top-4">
                        <div class="card-body">
                            <h3 class="card-title">Estado da Encomenda</h3>

                            <div class="mt-4">
                                @php
                                    $badgeClass = match($encomenda->estado) {
                                        'pendente' => 'badge-warning',
                                        'paga' => 'badge-success',
                                        'processando' => 'badge-info',
                                        'enviada' => 'badge-primary',
                                        'entregue' => 'badge-success',
                                        'cancelada' => 'badge-error',
                                        default => 'badge-ghost'
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }} badge-lg">{{ ucfirst($encomenda->estado) }}</span>
                            </div>

                            @if(auth()->user()->isAdmin())
                                <div class="divider"></div>

                                <h4 class="font-bold">Atualizar Estado</h4>
                                <form method="POST" action="{{ route('encomendas.updateStatus', $encomenda) }}">
                                    @csrf
                                    @method('PATCH')

                                    <select name="estado" class="select select-bordered w-full mt-2">
                                        <option value="pendente" {{ $encomenda->estado === 'pendente' ? 'selected' : '' }}>Pendente</option>
                                        <option value="paga" {{ $encomenda->estado === 'paga' ? 'selected' : '' }}>Paga</option>
                                        <option value="processando" {{ $encomenda->estado === 'processando' ? 'selected' : '' }}>Processando</option>
                                        <option value="enviada" {{ $encomenda->estado === 'enviada' ? 'selected' : '' }}>Enviada</option>
                                        <option value="entregue" {{ $encomenda->estado === 'entregue' ? 'selected' : '' }}>Entregue</option>
                                        <option value="cancelada" {{ $encomenda->estado === 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                                    </select>

                                    <button type="submit" class="btn btn-primary w-full mt-3">
                                        Atualizar Estado
                                    </button>
                                </form>
                            @endif

                            @if($encomenda->notas)
                                <div class="divider"></div>
                                <h4 class="font-bold">Notas</h4>
                                <p class="text-sm">{{ $encomenda->notas }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
