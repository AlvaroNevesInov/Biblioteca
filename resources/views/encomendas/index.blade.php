<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-base-content leading-tight">
                @if(auth()->user()->isAdmin())
                    Gestão de Encomendas
                @else
                    Minhas Encomendas
                @endif
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

            @if($encomendas->isEmpty())
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 mx-auto text-base-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="text-2xl font-bold mt-4">Nenhuma encomenda encontrada</h3>
                        <p class="text-base-content/70">Ainda não realizou nenhuma encomenda.</p>
                        <div class="card-actions justify-center mt-4">
                            <a href="{{ route('livros.index') }}" class="btn btn-primary">Começar a Comprar</a>
                        </div>
                    </div>
                </div>
            @else
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <div class="overflow-x-auto">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Número</th>
                                        @if(auth()->user()->isAdmin())
                                            <th>Cliente</th>
                                        @endif
                                        <th>Data</th>
                                        <th>Items</th>
                                        <th class="text-right">Total</th>
                                        <th>Estado</th>
                                        <th class="text-right">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($encomendas as $encomenda)
                                        <tr>
                                            <td class="font-mono font-bold">{{ $encomenda->numero_encomenda }}</td>
                                            @if(auth()->user()->isAdmin())
                                                <td>
                                                    <div>
                                                        <div class="font-bold">{{ $encomenda->user->name }}</div>
                                                        <div class="text-sm opacity-50">{{ $encomenda->user->email }}</div>
                                                    </div>
                                                </td>
                                            @endif
                                            <td>{{ $encomenda->created_at->format('d/m/Y H:i') }}</td>
                                            <td>{{ $encomenda->items->count() }}</td>
                                            <td class="text-right font-bold text-primary">€{{ number_format($encomenda->total, 2) }}</td>
                                            <td>
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
                                                <span class="badge {{ $badgeClass }}">{{ ucfirst($encomenda->estado) }}</span>
                                            </td>
                                            <td class="text-right">
                                                <a href="{{ route('encomendas.show', $encomenda) }}" class="btn btn-sm btn-ghost">
                                                    Ver Detalhes
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginação -->
                        @if($encomendas->hasPages())
                            <div class="mt-6">
                                {{ $encomendas->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
