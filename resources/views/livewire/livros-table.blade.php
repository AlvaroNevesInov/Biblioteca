<div class="p-6">
    <div class="flex justify-between mb-4 gap-4">
        <div class="flex-1">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Pesquisar por nome ou ISBN..."
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            >
        </div>
        <div class="w-48">
            <select wire:model.live="filterEditora" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">Todas as Editoras</option>
                @foreach($editoras as $editora)
                    <option value="{{ $editora->id }}">{{ $editora->nome }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-32">
            <select wire:model.live="perPage" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="5">5 por página</option>
                <option value="10">10 por página</option>
                <option value="25">25 por página</option>
                <option value="50">50 por página</option>
                <option value="100">100 por página</option>
            </select>
        </div>

        @if($this->isAdmin)
        <div>
            <a href="{{ route('livros.create') }}" class="btn btn-primary gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Novo Livro
            </a>
        </div>
        @endif
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th wire:click="sortBy('isbn')" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 w-32">
                        ISBN
                        @if($sortField === 'isbn')
                            <span>{!! $sortDirection === 'asc' ? '&#8593;' : '&#8595;' !!}</span>
                        @endif
                    </th>
                    <th wire:click="sortBy('nome')" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 min-w-[200px]">
                        Nome
                        @if($sortField === 'nome')
                            <span>{!! $sortDirection === 'asc' ? '&#8593;' : '&#8595;' !!}</span>
                        @endif
                    </th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                        Editora
                    </th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[150px]">
                        Autores
                    </th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[200px]">
                        Bibliografia
                    </th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-24">
                        Capa
                    </th>
                    <th wire:click="sortBy('preco')" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 w-24">
                        Preço
                        @if($sortField === 'preco')
                            <span>{!! $sortDirection === 'asc' ? '&#8593;' : '&#8595;' !!}</span>
                        @endif
                    </th>
                    @if($this->isAdmin)
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-48">
                        Ações
                    </th>
                    @endif
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($livros as $livro)
                    <tr>
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                            {{ $livro->isbn }}
                        </td>
                        <td class="px-4 py-4 text-sm font-medium text-gray-900 text-center">
                            {{ $livro->nome }}
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                            {{ $livro->editora->nome ?? 'N/A' }}
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-500 text-center">
                            {{ $livro->autores->pluck('nome')->join(', ') ?: 'Sem autor' }}
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-500 text-center" title="{{ $livro->bibliografia }}">
                            {{ Str::limit($livro->bibliografia, 50, '...') }}
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-center">
                            <div class="flex justify-center">
                                @if($livro->imagem_capa)
                                    <img src="{{ $livro->imagem_capa }}" alt="Capa de {{ $livro->nome }}" class="h-16 w-12 object-cover rounded shadow-sm" loading="lazy">
                                @else
                                    <span class="text-gray-400 text-xs">Sem imagem</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                            {{ $livro->preco ? '€ ' . number_format($livro->preco, 2, ',', '.') : 'N/A' }}
                        </td>
                        @if($this->isAdmin)
                            <td class="px-4 py-4 whitespace-nowrap text-center">
                                <div class="flex justify-center gap-2">
                                    <a href="{{ route('livros.edit', $livro->id) }}" class="inline-flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded shadow-sm transition" title="Editar">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Editar
                                    </a>
                                    <button
                                        wire:click="confirmDelete({{ $livro->id }})"
                                        class="inline-flex items-center px-3 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded shadow-sm transition"
                                        title="Eliminar"
                                        type="button"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Eliminar
                                    </button>
                                </div>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $this->isAdmin ? 8 : 7 }}" class="px-4 py-4 text-center text-gray-500">
                            Nenhum livro encontrado.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4 flex items-center justify-between">
        <div class="text-sm text-gray-700">
            Mostrando <span class="font-medium">{{ $livros->firstItem() ?? 0 }}</span> a <span class="font-medium">{{ $livros->lastItem() ?? 0 }}</span> de <span class="font-medium">{{ $livros->total() }}</span> resultados
        </div>
        <div>
            {{ $livros->links() }}
        </div>
    </div>

    @if($confirmingDelete)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Confirmar Eliminação</h3>
            <p class="text-gray-600 mb-6">
                Tem a certeza que deseja eliminar este livro? Esta ação não pode ser revertida.
            </p>
            <div class="flex justify-end gap-3">
                <button
                    wire:click="$set('confirmingDelete', false)"
                    class="btn btn-ghost"
                >
                    Cancelar
                </button>
                <button
                    wire:click="delete"
                    class="btn btn-error"
                >
                    Eliminar
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
