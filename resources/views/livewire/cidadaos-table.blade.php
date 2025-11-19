<div class="p-6">
    <!-- Filtros e Pesquisa -->
    <div class="flex flex-col sm:flex-row justify-between gap-4 mb-4">
        <!-- Pesquisa -->
        <div class="form-control w-full sm:w-auto">
            <div class="input-group">
                <input type="text"
                       placeholder="Pesquisar por nome ou email..."
                       class="input input-bordered w-full sm:w-64"
                       wire:model.live.debounce.300ms="search">
                <button class="btn btn-square">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Filtros -->
        <div class="flex gap-2">
            <select class="select select-bordered" wire:model.live="filterRole">
                <option value="">Todos os tipos</option>
                <option value="admin">Administrador</option>
                <option value="cidadao">Cidadão</option>
            </select>

            <select class="select select-bordered" wire:model.live="perPage">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
        </div>
    </div>

    <!-- Tabela -->
    <div class="overflow-x-auto">
        <table class="table table-zebra w-full">
            <thead>
                <tr>
                    <th>Foto</th>
                    <th class="cursor-pointer" wire:click="sortBy('name')">
                        <div class="flex items-center gap-2">
                            Nome
                            @if($sortField === 'name')
                                @if($sortDirection === 'asc')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                @endif
                            @endif
                        </div>
                    </th>
                    <th class="cursor-pointer" wire:click="sortBy('email')">
                        <div class="flex items-center gap-2">
                            Email
                            @if($sortField === 'email')
                                @if($sortDirection === 'asc')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                @endif
                            @endif
                        </div>
                    </th>
                    <th>Tipo</th>
                    <th>Requisições Ativas</th>
                    <th>Total Requisições</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cidadaos as $cidadao)
                    <tr>
                        <td>
                            @if($cidadao->profile_photo_path)
                                <div class="avatar">
                                    <div class="w-10 rounded-full">
                                        <img src="{{ $cidadao->profile_photo_url }}" alt="{{ $cidadao->name }}">
                                    </div>
                                </div>
                            @else
                                <div class="avatar placeholder">
                                    <div class="bg-neutral-focus text-neutral-content rounded-full w-10">
                                        <span class="text-xs">{{ substr($cidadao->name, 0, 2) }}</span>
                                    </div>
                                </div>
                            @endif
                        </td>
                        <td class="font-medium">{{ $cidadao->name }}</td>
                        <td>{{ $cidadao->email }}</td>
                        <td>
                            @if($cidadao->role === 'admin')
                                <span class="badge badge-primary">Admin</span>
                            @else
                                <span class="badge badge-info">Cidadão</span>
                            @endif
                        </td>
                        <td>
                            <span class="font-semibold {{ $cidadao->requisicoes_ativas_count >= 3 ? 'text-warning' : '' }}">
                                {{ $cidadao->requisicoes_ativas_count }} / 3
                            </span>
                            @if($cidadao->requisicoes_ativas_count >= 3)
                                <span class="badge badge-warning badge-xs ml-1">Limite</span>
                            @endif
                        </td>
                        <td>{{ $cidadao->total_requisicoes_count }}</td>
                        <td>
                            <a href="{{ route('cidadaos.show', $cidadao) }}" class="btn btn-sm btn-ghost">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                Ver Detalhes
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-8">
                            <div class="text-base-content/70">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-2 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <p>Nenhum utilizador encontrado.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginação -->
    <div class="mt-4">
        {{ $cidadaos->links() }}
    </div>
</div>
