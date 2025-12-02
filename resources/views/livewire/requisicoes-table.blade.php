<div class="p-6">
    <!-- Indicadores -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <!-- Requisições Ativas -->
        <div class="bg-gradient-to-br from-green-50 to-green-100 border border-green-200 rounded-lg p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-green-600 mb-1">Requisições Ativas</p>
                    <p class="text-3xl font-bold text-green-700">{{ $requisicoesAtivas }}</p>
                </div>
                <div class="bg-green-500 text-white p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Requisições Últimos 30 Dias -->
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-lg p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-blue-600 mb-1">Últimos 30 Dias</p>
                    <p class="text-3xl font-bold text-blue-700">{{ $requisicoesUltimos30Dias }}</p>
                </div>
                <div class="bg-blue-500 text-white p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Livros Entregues Hoje -->
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 border border-purple-200 rounded-lg p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-purple-600 mb-1">Livros Entregues Hoje</p>
                    <p class="text-3xl font-bold text-purple-700">{{ $livrosEntreguesHoje }}</p>
                </div>
                <div class="bg-purple-500 text-white p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

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

                                    @if($requisicao->isDevolvida() && !$requisicao->isRecebido())
                                        <button
                                            onclick="document.getElementById('recepcao-modal-{{ $requisicao->id }}').showModal()"
                                            class="btn btn-xs btn-primary gap-1"
                                            title="Confirmar Recepcao"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                            </svg>
                                            Confirmar
                                        </button>

                                        <!-- Modal de Confirmacao de Recepcao -->
                                        <dialog id="recepcao-modal-{{ $requisicao->id }}" class="modal">
                                            <div class="modal-box">
                                                <h3 class="font-bold text-lg">Confirmar Recepcao do Livro</h3>
                                                <form action="{{ route('requisicoes.confirmar-recepcao', $requisicao) }}" method="POST" class="py-4">
                                                    @csrf
                                                    @method('PATCH')
                                                    <div class="form-control">
                                                        <label class="label">
                                                            <span class="label-text">Data de Recepcao</span>
                                                        </label>
                                                        <input
                                                            type="date"
                                                            name="data_recepcao"
                                                            class="input input-bordered"
                                                            value="{{ now()->format('Y-m-d') }}"
                                                            min="{{ $requisicao->data_requisicao->format('Y-m-d') }}"
                                                            max="{{ now()->format('Y-m-d') }}"
                                                            required
                                                        >
                                                    </div>
                                                    <div class="mt-3 text-sm text-gray-500">
                                                        <p><strong>Data da Requisicao:</strong> {{ $requisicao->data_requisicao->format('d/m/Y') }}</p>
                                                        <p><strong>Data Prevista:</strong> {{ $requisicao->data_prevista_devolucao->format('d/m/Y') }}</p>
                                                    </div>
                                                    <div class="modal-action">
                                                        <button type="button" class="btn" onclick="document.getElementById('recepcao-modal-{{ $requisicao->id }}').close()">Cancelar</button>
                                                        <button type="submit" class="btn btn-primary">Confirmar Recepcao</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </dialog>
                                    @endif

                                    @if($requisicao->isDevolvida() && $requisicao->isRecebido())
                                        <button
                                            onclick="document.getElementById('info-recepcao-modal-{{ $requisicao->id }}').showModal()"
                                            class="btn btn-xs btn-ghost gap-1"
                                            title="Ver Detalhes da Recepcao"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Recebido
                                        </button>

                                        <!-- Modal de Informacao da Recepcao -->
                                        <dialog id="info-recepcao-modal-{{ $requisicao->id }}" class="modal">
                                            <div class="modal-box">
                                                <h3 class="font-bold text-lg">Detalhes da Recepcao</h3>
                                                <div class="py-4 space-y-2">
                                                    <p><strong>Data de Recepcao:</strong> {{ $requisicao->data_recepcao->format('d/m/Y') }}</p>
                                                    <p><strong>Dias Decorridos:</strong> {{ $requisicao->diasDecorridos() }} dias</p>
                                                    @if($requisicao->diasAtraso() > 0)
                                                        <p class="text-error"><strong>Dias de Atraso:</strong> {{ $requisicao->diasAtraso() }} dias</p>
                                                    @else
                                                        <p class="text-success"><strong>Entregue dentro do prazo</strong></p>
                                                    @endif
                                                    @if($requisicao->recebidoPor)
                                                        <p><strong>Recebido por:</strong> {{ $requisicao->recebidoPor->name }}</p>
                                                    @endif
                                                </div>
                                                <div class="modal-action">
                                                    <button class="btn" onclick="document.getElementById('info-recepcao-modal-{{ $requisicao->id }}').close()">Fechar</button>
                                                </div>
                                            </div>
                                        </dialog>
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

                                    @if($requisicao->isDevolvida() && !$requisicao->hasReview())
                                        <button
                                            onclick="document.getElementById('review-modal-{{ $requisicao->id }}').showModal()"
                                            class="btn btn-xs btn-primary gap-1"
                                            title="Deixar Review"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                            </svg>
                                            Review
                                        </button>

                                        <!-- Modal de Review -->
                                        <dialog id="review-modal-{{ $requisicao->id }}" class="modal">
                                            <div class="modal-box">
                                                <h3 class="font-bold text-lg">Deixar Review - {{ $requisicao->livro->nome }}</h3>
                                                <form action="{{ route('reviews.store') }}" method="POST" class="py-4">
                                                    @csrf
                                                    <input type="hidden" name="requisicao_id" value="{{ $requisicao->id }}">
                                                    <div class="form-control">
                                                        <label class="label">
                                                            <span class="label-text">O seu comentário *</span>
                                                        </label>
                                                        <textarea
                                                            name="comentario"
                                                            class="textarea textarea-bordered h-32"
                                                            placeholder="Partilhe a sua opinião sobre este livro..."
                                                            required
                                                            minlength="10"
                                                            maxlength="1000"
                                                        ></textarea>
                                                        <label class="label">
                                                            <span class="label-text-alt">Mínimo 10 caracteres, máximo 1000</span>
                                                        </label>
                                                    </div>
                                                    <div class="alert alert-info mt-4">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                        <span class="text-sm">O seu review será analisado por um administrador antes de ser publicado.</span>
                                                    </div>
                                                    <div class="modal-action">
                                                        <button type="button" class="btn" onclick="document.getElementById('review-modal-{{ $requisicao->id }}').close()">Cancelar</button>
                                                        <button type="submit" class="btn btn-primary">Submeter Review</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </dialog>
                                    @endif

                                    @if($requisicao->review)
                                        <button
                                            onclick="document.getElementById('review-status-modal-{{ $requisicao->id }}').showModal()"
                                            class="btn btn-xs btn-ghost gap-1"
                                            title="Ver Review"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                            </svg>
                                            @if($requisicao->review->isSuspenso())
                                                <span class="badge badge-warning badge-xs">Suspenso</span>
                                            @elseif($requisicao->review->isAtivo())
                                                <span class="badge badge-success badge-xs">Ativo</span>
                                            @else
                                                <span class="badge badge-error badge-xs">Recusado</span>
                                            @endif
                                        </button>

                                        <!-- Modal de Status do Review -->
                                        <dialog id="review-status-modal-{{ $requisicao->id }}" class="modal">
                                            <div class="modal-box">
                                                <h3 class="font-bold text-lg">O seu Review</h3>
                                                <div class="py-4 space-y-3">
                                                    <div>
                                                        <p class="text-sm text-gray-600 mb-1">Estado:</p>
                                                        @if($requisicao->review->isSuspenso())
                                                            <span class="badge badge-warning">Aguardando Aprovação</span>
                                                        @elseif($requisicao->review->isAtivo())
                                                            <span class="badge badge-success">Aprovado e Publicado</span>
                                                        @else
                                                            <span class="badge badge-error">Recusado</span>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <p class="text-sm text-gray-600 mb-1">O seu comentário:</p>
                                                        <p class="whitespace-pre-wrap bg-base-200 p-3 rounded">{{ $requisicao->review->comentario }}</p>
                                                    </div>
                                                    @if($requisicao->review->isRecusado() && $requisicao->review->justificacao_recusa)
                                                        <div class="alert alert-error">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                            <div>
                                                                <p class="font-semibold">Motivo da recusa:</p>
                                                                <p class="text-sm">{{ $requisicao->review->justificacao_recusa }}</p>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="modal-action">
                                                    <button class="btn" onclick="document.getElementById('review-status-modal-{{ $requisicao->id }}').close()">Fechar</button>
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
