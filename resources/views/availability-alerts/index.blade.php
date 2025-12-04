<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-base-content leading-tight">
                {{ __('Meus Alertas de Disponibilidade') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-base-100 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    @if($alertas->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="table table-zebra w-full">
                                <thead>
                                    <tr>
                                        <th>Livro</th>
                                        <th>Capa</th>
                                        <th>ISBN</th>
                                        <th>Autores</th>
                                        <th>Editora</th>
                                        <th>Data Alerta</th>
                                        <th>Estado</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($alertas as $alerta)
                                        <tr>
                                            <td>
                                                <a href="{{ route('livros.show', $alerta->livro) }}" class="link link-primary font-medium">
                                                    {{ $alerta->livro->nome }}
                                                </a>
                                            </td>
                                            <td>
                                                @if($alerta->livro->imagem_capa)
                                                    <img src="{{ asset('storage/' . $alerta->livro->imagem_capa) }}"
                                                         alt="{{ $alerta->livro->nome }}"
                                                         class="w-12 h-16 object-cover rounded shadow">
                                                @else
                                                    <div class="w-12 h-16 bg-base-300 rounded flex items-center justify-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-base-content/30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                                        </svg>
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="font-mono text-sm">{{ $alerta->livro->isbn }}</td>
                                            <td>{{ $alerta->livro->autores->pluck('nome')->join(', ') }}</td>
                                            <td>{{ $alerta->livro->editora->nome ?? 'N/A' }}</td>
                                            <td>{{ $alerta->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                @if($alerta->livro->estaDisponivel())
                                                    <div class="badge badge-success gap-2">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                        Disponível
                                                    </div>
                                                @else
                                                    <div class="badge badge-warning gap-2">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        Indisponível
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="flex gap-2">
                                                    @if($alerta->livro->estaDisponivel())
                                                        <a href="{{ route('requisicoes.create', ['livro_id' => $alerta->livro->id]) }}" class="btn btn-sm btn-primary">
                                                            Requisitar
                                                        </a>
                                                    @endif
                                                    <form action="{{ route('availability-alerts.destroy') }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <input type="hidden" name="livro_id" value="{{ $alerta->livro->id }}">
                                                        <button type="submit" class="btn btn-sm btn-error" onclick="return confirm('Tem certeza que deseja remover este alerta?')">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto mb-4 text-base-content/30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <p class="text-base-content/70 text-lg mb-4">Não tem alertas de disponibilidade ativos</p>
                            <p class="text-base-content/50 mb-6">Quando visualiza um livro indisponível, pode solicitar um alerta para ser notificado quando ele ficar disponível.</p>
                            <a href="{{ route('livros.index') }}" class="btn btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                                Explorar Livros
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
