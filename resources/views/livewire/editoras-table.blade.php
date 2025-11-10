<div class="p-6">
    <div class="flex justify-between mb-4">
        <div class="flex-1">
            <input
                type="text"
                wire:model.debounce.300ms="search"
                placeholder="Pesquisar editoras..."
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            >
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th wire:click="sortBy('nome')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                        Nome
                        @if($sortField === 'nome')
                            <span>{!! $sortDirection === 'asc' ? '&#8593;' : '&#8595;' !!}</span>
                        @endif
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Logotipo
                    </th>

                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($editoras as $editora)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $editora->nome }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($editora->logotipo)
                                <img src="{{ Storage::url($editora->logotipo) }}" alt="{{ $editora->nome }}" class="h-10 w-auto object-contain">
                            @else
                                <span class="text-gray-400 text-sm">Sem logotipo</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                            Nenhuma editora encontrada.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $editoras->links() }}
    </div>
</div>
