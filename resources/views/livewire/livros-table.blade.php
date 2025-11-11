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
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 table-auto">
            <thead class="bg-gray-50">
                <tr>
                    <th wire:click="sortBy('isbn')" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 w-auto">
                        ISBN
                        @if($sortField === 'isbn')
                            <span>{!! $sortDirection === 'asc' ? '&#8593;' : '&#8595;' !!}</span>
                        @endif
                    </th>
                    <th wire:click="sortBy('nome')" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 w-auto">
                        Nome
                        @if($sortField === 'nome')
                            <span>{!! $sortDirection === 'asc' ? '&#8593;' : '&#8595;' !!}</span>
                        @endif
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-auto">
                        Editora
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-auto">
                        Autores
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-auto">
                        Bibliografia
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-24">
                        Capa
                    </th>
                    <th wire:click="sortBy('preco')" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 w-32">
                        Preço
                        @if($sortField === 'preco')
                            <span>{!! $sortDirection === 'asc' ? '&#8593;' : '&#8595;' !!}</span>
                        @endif
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($livros as $livro)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                            {{ $livro->isbn }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-center">
                            {{ $livro->nome }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                            {{ $livro->editora->nome ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 text-center">
                            {{ $livro->autores->pluck('nome')->join(', ') }}
                        </td>
                         <td class="px-6 py-4 text-sm text-gray-500 max-w-xs text-center" title="{{ $livro->bibliografia }}">
                            {{ Str::limit($livro->bibliografia, 60, '...') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="flex justify-center">
                                @if($livro->imagem_capa)
                                    <img src="{{ $livro->imagem_capa }}" alt="Capa de {{ $livro->nome }}" class="h-16 rounded shadow-sm">
                                @else
                                    <span class="text-gray-400 text-sm">Sem imagem</span>
                                @endif
                            </div>
                        </td>
                        <td class="py-4 whitespace-nowrap text-sm text-gray-900 w-32 text-center">
                            {{ $livro->preco ? '€ ' . number_format($livro->preco, 2, ',', '.') : 'N/A' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
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
</div>
