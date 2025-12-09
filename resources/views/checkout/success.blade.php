<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-base-content leading-tight">
                Encomenda Confirmada
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Mensagem de Sucesso -->
            <div class="alert alert-success mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-8 w-8" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <div>
                    <h3 class="font-bold text-lg">Pagamento Confirmado!</h3>
                    <div class="text-sm">Obrigado pela sua encomenda. Receberá um email de confirmação em breve.</div>
                </div>
            </div>

            <!-- Detalhes da Encomenda -->
            <div class="card bg-base-100 shadow-xl mb-6">
                <div class="card-body">
                    <h3 class="card-title text-2xl">Detalhes da Encomenda</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div>
                            <p class="text-sm text-base-content/70">Número da Encomenda</p>
                            <p class="font-bold text-lg">{{ $encomenda->numero_encomenda }}</p>
                        </div>

                        <div>
                            <p class="text-sm text-base-content/70">Data</p>
                            <p class="font-bold">{{ $encomenda->created_at->format('d/m/Y H:i') }}</p>
                        </div>

                        <div>
                            <p class="text-sm text-base-content/70">Estado</p>
                            <span class="badge badge-success">{{ ucfirst($encomenda->estado) }}</span>
                        </div>

                        <div>
                            <p class="text-sm text-base-content/70">Total</p>
                            <p class="font-bold text-primary text-lg">€{{ number_format($encomenda->total, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Morada de Entrega -->
            <div class="card bg-base-100 shadow-xl mb-6">
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
            <div class="card bg-base-100 shadow-xl mb-6">
                <div class="card-body">
                    <h3 class="card-title">Items da Encomenda</h3>

                    <div class="overflow-x-auto mt-4">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Livro</th>
                                    <th class="text-center">Quantidade</th>
                                    <th class="text-right">Preço Unitário</th>
                                    <th class="text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($encomenda->items as $item)
                                    <tr>
                                        <td>
                                            <div class="flex items-center gap-3">
                                                @if($item->livro->imagem_capa)
                                                    <div class="avatar">
                                                        <div class="mask mask-squircle w-12 h-12">
                                                            <img src="{{ $item->livro->imagem_capa }}" alt="{{ $item->livro->nome }}" />
                                                        </div>
                                                    </div>
                                                @endif
                                                <div>
                                                    <div class="font-bold">{{ $item->livro->nome }}</div>
                                                    <div class="text-sm opacity-50">{{ $item->livro->autores->pluck('nome')->join(', ') }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">{{ $item->quantidade }}</td>
                                        <td class="text-right">€{{ number_format($item->preco_unitario, 2) }}</td>
                                        <td class="text-right font-bold">€{{ number_format($item->subtotal, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-right">Subtotal</th>
                                    <th class="text-right">€{{ number_format($encomenda->subtotal, 2) }}</th>
                                </tr>
                                <tr>
                                    <th colspan="3" class="text-right">Taxas</th>
                                    <th class="text-right">€{{ number_format($encomenda->taxas, 2) }}</th>
                                </tr>
                                <tr>
                                    <th colspan="3" class="text-right text-lg">Total</th>
                                    <th class="text-right text-lg text-primary">€{{ number_format($encomenda->total, 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Botões de Ação -->
            <div class="flex justify-between">
                <a href="{{ route('livros.index') }}" class="btn btn-ghost">
                    Continuar a Comprar
                </a>
                <a href="{{ route('encomendas.show', $encomenda) }}" class="btn btn-primary">
                    Ver Detalhes da Encomenda
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
