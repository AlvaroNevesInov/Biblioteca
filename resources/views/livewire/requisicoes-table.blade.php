<div class="p-6">
    <div class="flex justify-between mb-4 gap-4 flex-wrap">
        <!-- Pesquisa -->
        <div class="flex-1 min-w-[200px]">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Pesquisar por livro ou utilizador..."
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            >
        </div>

        <!-- Filtro de Estado -->
        <div class="w-48">
            <select wire:model.live="filterEstado" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">Todos os Estados</option>
                <option value="pendente">Pendente</option>
                <option value="aprovada">Aprovada</option>
                <option value="rejeitada">Rejeitada</option>
                <option value="devolvida">Devolvida</option>
            </select>
        </div>

        <!-- Items por página -->
        <div class="w-32">
            <select wire:model.live="perPage" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="5">5 por página</option>
                <option value="10">10 por página</option>
                <option value="25">25 por página</option>
                <option value="50">50 por página</option>
            </select>
        </div>
    </div>

    <!-- Tabela -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th wire:click="sortBy('id')" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                        ID
                        @if($sortField === 'id')
                            <span>{!! $sortDirection === 'asc' ? '&#8593;' : '&#8595;' !!}</span>
                        @endif
                    </th>
                    @if($isAdmin)
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Utilizador
                        </th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">

                            Foto

                        </th>
                    @endif
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Livro
                    </th>
                    <th wire:click="sortBy('estado')" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                        Estado
                        @if($sortField === 'estado')
                            <span>{!! $sortDirection === 'asc' ? '&#8593;' : '&#8595;' !!}</span>
                        @endif
                    </th>
                    <th wire:click="sortBy('data_requisicao')" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                        Data Requisição
                        @if($sortField === 'data_requisicao')
                            <span>{!! $sortDirection === 'asc' ? '&#8593;' : '&#8595;' !!}</span>
                        @endif
                    </th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Data Prev. Devolução
                    </th>
                    @if($isAdmin)
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Ações
                        </th>
                    @else
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Ações
                        </th>
                    @endif
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($requisicoes as $requisicao)
                    <tr>
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                            #{{ $requisicao->id }}
                        </td>
                        @if($isAdmin)
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                 <a href="{{ route('cidadaos.show', $requisicao->user) }}" class="text-blue-600 hover:text-blue-800 hover:underline">
                                    {{ $requisicao->user->name }}
                                </a>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-center">
                                @if($requisicao->foto_cidadao)
                                    <div class="avatar">
                                        <div class="w-[50px] h-[50px] rounded-full">
                                            <img src="{{ $requisicao->foto_cidadao }}" alt="{{ $requisicao->user->name }}">
                                        </div>
                                    </div>
                                @elseif($requisicao->user->profile_photo_path)
                                    <div class="avatar">
                                        <div class="w-10 rounded-full">
                                            <img src="{{ $requisicao->user->profile_photo_url }}" alt="{{ $requisicao->user->name }}">
                                        </div>
                                    </div>
                                @else
                                    <div class="avatar placeholder">
                                        <div class="bg-neutral-focus text-neutral-content rounded-full w-10">
                                            <span class="text-xs">{{ substr($requisicao->user->name, 0, 2) }}</span>
                                        </div>
                                    </div>
                                @endif
                            </td>
                        @endif
                        <td class="px-4 py-4 text-sm text-gray-900 text-center">
                            <a href="{{ route('livros.show', $requisicao->livro) }}" class="text-blue-600 hover:text-blue-800 hover:underline">
                                <div class="font-medium">{{ $requisicao->livro->nome }}</div>
                            </a>
                            <div class="text-xs text-gray-500">{{ $requisicao->livro->isbn }}</div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-center">
                            @if($requisicao->estado === 'pendente')
                                <span class="badge badge-warning gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Pendente
                                </span>
                            @elseif($requisicao->estado === 'aprovada')
                                <span class="badge badge-success gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Aprovada
                                </span>
                            @elseif($requisicao->estado === 'rejeitada')
                                <span class="badge badge-error gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Rejeitada
                                </span>
                            @else
                                <span class="badge badge-info gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Devolvida
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                            {{ $requisicao->data_requisicao->format('d/m/Y') }}
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                            {{ $requisicao->data_prevista_devolucao ? $requisicao->data_prevista_devolucao->format('d/m/Y') : '-' }}
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-center">
                            <div class="flex justify-center gap-2">
                                @if($isAdmin)
                                    <!-- Ações para Admin -->
                                    @if($requisicao->isPendente())
                                        <form action="{{ route('requisicoes.aprovar', $requisicao) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-xs btn-success gap-1" title="Aprovar">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                Aprovar
                                            </button>
                                        </form>
                                        <button
                                            onclick="document.getElementById('reject-modal-{{ $requisicao->id }}').showModal()"
                                            class="btn btn-xs btn-error gap-1"
                                            title="Rejeitar"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                            Rejeitar
                                        </button>

                                        <!-- Modal de Rejeição -->
                                        <dialog id="reject-modal-{{ $requisicao->id }}" class="modal">
                                            <div class="modal-box">
                                                <h3 class="font-bold text-lg">Rejeitar Requisição</h3>
                                                <form action="{{ route('requisicoes.rejeitar', $requisicao) }}" method="POST" class="py-4">
                                                    @csrf
                                                    @method('PATCH')
                                                    <div class="form-control">
                                                        <label class="label">
                                                            <span class="label-text">Motivo da rejeição (opcional)</span>
                                                        </label>
                                                        <textarea
                                                            name="observacoes"
                                                            class="textarea textarea-bordered"
                                                            rows="3"
                                                            placeholder="Adicione um motivo para a rejeição..."
                                                        ></textarea>
                                                    </div>
                                                    <div class="modal-action">
                                                        <button type="button" class="btn" onclick="document.getElementById('reject-modal-{{ $requisicao->id }}').close()">Cancelar</button>
                                                        <button type="submit" class="btn btn-error">Confirmar Rejeição</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </dialog>
                                    @endif

                                    @if($requisicao->isAprovada())
                                        <form action="{{ route('requisicoes.devolver', $requisicao) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-xs btn-info gap-1" title="Marcar como Devolvido">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                Devolvido
                                            </button>
                                        </form>
                                    @endif

                                    @if($requisicao->observacoes)
                                        <button
                                            onclick="document.getElementById('obs-modal-{{ $requisicao->id }}').showModal()"
                                            class="btn btn-xs btn-ghost gap-1"
                                            title="Ver Observações"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </button>

                                        <!-- Modal de Observações -->
                                        <dialog id="obs-modal-{{ $requisicao->id }}" class="modal">
                                            <div class="modal-box">
                                                <h3 class="font-bold text-lg">Observações</h3>
                                                <p class="py-4">{{ $requisicao->observacoes }}</p>
                                                <div class="modal-action">
                                                    <button class="btn" onclick="document.getElementById('obs-modal-{{ $requisicao->id }}').close()">Fechar</button>
                                                </div>
                                            </div>
                                        </dialog>
                                    @endif
                                @else
                                    <!-- Ações para Cidadão -->
                                    @if($requisicao->isPendente())
                                        <button
                                            wire:click="confirmDelete({{ $requisicao->id }})"
                                            class="btn btn-xs btn-error gap-1"
                                            title="Cancelar Requisição"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                            Cancelar
                                        </button>
                                    @endif

                                    @if($requisicao->observacoes)
                                        <button
                                            onclick="document.getElementById('obs-modal-{{ $requisicao->id }}').showModal()"
                                            class="btn btn-xs btn-ghost gap-1"
                                            title="Ver Observações"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </button>

                                        <!-- Modal de Observações -->
                                        <dialog id="obs-modal-{{ $requisicao->id }}" class="modal">
                                            <div class="modal-box">
                                                <h3 class="font-bold text-lg">Observações</h3>
                                                <p class="py-4">{{ $requisicao->observacoes }}</p>
                                                <div class="modal-action">
                                                    <button class="btn" onclick="document.getElementById('obs-modal-{{ $requisicao->id }}').close()">Fechar</button>
                                                </div>
                                            </div>
                                        </dialog>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $isAdmin ? 8 : 6 }}" class="px-4 py-4 text-center text-gray-500">
                            Nenhuma requisição encontrada.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginação -->
    <div class="mt-4 flex items-center justify-between">
        <div class="text-sm text-gray-700">
            Mostrando <span class="font-medium">{{ $requisicoes->firstItem() ?? 0 }}</span> a <span class="font-medium">{{ $requisicoes->lastItem() ?? 0 }}</span> de <span class="font-medium">{{ $requisicoes->total() }}</span> resultados
        </div>
        <div>
            {{ $requisicoes->links() }}
        </div>
    </div>

    <!-- Modal de Confirmação de Cancelamento -->
    @if($confirmingDelete)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Confirmar Cancelamento</h3>
            <p class="text-gray-600 mb-6">
                Tem a certeza que deseja cancelar esta requisição? Esta ação não pode ser revertida.
            </p>
            <div class="flex justify-end gap-3">
                <button
                    wire:click="$set('confirmingDelete', false)"
                    class="btn btn-ghost"
                >
                    Não, voltar
                </button>
                <button
                    wire:click="delete"
                    class="btn btn-error"
                >
                    Sim, cancelar requisição
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
