<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-base-content leading-tight">
                {{ __('Detalhes do Cidadão') }}
            </h2>
            <a href="{{ route('cidadaos.index') }}" class="btn btn-ghost btn-sm gap-2">

                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>

                Voltar
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Informações do Cidadão -->
            <div class="bg-base-100 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <div class="flex flex-col md:flex-row gap-6 items-start">

                        <!-- Foto do Cidadão -->
                        <div class="flex-shrink-0">
                            @if($cidadao->profile_photo_path)
                                <img src="{{ $cidadao->profile_photo_url }}"
                                     alt="{{ $cidadao->name }}"
                                     class="w-32 h-32 object-cover rounded-full shadow-md">
                            @else
                                <div class="avatar placeholder">
                                    <div class="bg-neutral-focus text-neutral-content rounded-full w-32 h-32">
                                        <span class="text-3xl">{{ substr($cidadao->name, 0, 2) }}</span>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Detalhes do Cidadão -->
                        <div class="flex-grow space-y-4">
                            <h3 class="text-2xl font-bold text-base-content">{{ $cidadao->name }}</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <span class="text-sm font-medium text-base-content/70">Email:</span>
                                    <p class="text-base-content">{{ $cidadao->email }}</p>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-base-content/70">Tipo de Utilizador:</span>
                                    <p class="text-base-content">
                                        @if($cidadao->isAdmin())
                                            <span class="badge badge-primary">Administrador</span>
                                        @else
                                            <span class="badge badge-info">Cidadão</span>
                                        @endif
                                    </p>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-base-content/70">Membro desde:</span>
                                    <p class="text-base-content">{{ $cidadao->created_at->format('d/m/Y') }}</p>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-base-content/70">Livros Requisitados:</span>
                                    <p class="text-base-content">
                                        <span class="font-semibold">{{ $cidadao->contarRequisicoesAtivas() }}</span> / 3
                                        @if(!$cidadao->podeRequisitar())
                                            <span class="badge badge-warning badge-sm ml-2">Limite atingido</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Requisições Ativas -->

            <div class="bg-base-100 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-base-content mb-4 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>

                        Requisições Ativas ({{ $requisicoesAtivas->count() }})
                    </h3>

                    @if($requisicoesAtivas->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="table table-zebra w-full">
                                <thead>
                                    <tr>
                                        <th>Livro</th>
                                        <th>Foto na Requisição</th>
                                        <th>Estado</th>
                                        <th>Data Requisição</th>
                                        <th>Data Prevista Devolução</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($requisicoesAtivas as $requisicao)
                                        <tr>
                                            <td>
                                                <div class="flex items-center gap-3">
                                                    @if($requisicao->livro->imagem_capa)
                                                        <div class="avatar">
                                                            <div class="mask mask-squircle w-[50px] h-[50px]">
                                                                <img src="{{ asset('storage/' . $requisicao->livro->imagem_capa) }}" alt="{{ $requisicao->livro->nome }}">
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <div class="font-medium">{{ $requisicao->livro->nome }}</div>
                                                        <div class="text-sm text-base-content/70">{{ $requisicao->livro->isbn }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($requisicao->foto_cidadao)
                                                    <div class="avatar">
                                                        <div class="w-[50px] rounded-full">
                                                            <img src="{{ $requisicao->foto_cidadao }}" alt="{{ $cidadao->name }}">
                                                        </div>
                                                    </div>

                                                @else
                                                    <span class="text-base-content/50 text-sm">Sem foto</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($requisicao->isPendente())
                                                    <span class="badge badge-warning">Pendente</span>
                                                @else
                                                    <span class="badge badge-info">Aprovada</span>
                                                @endif
                                            </td>
                                            <td>{{ $requisicao->data_requisicao->format('d/m/Y') }}</td>
                                            <td>{{ $requisicao->data_prevista_devolucao?->format('d/m/Y') ?? 'N/D' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    @else
                        <div class="text-center py-8 text-base-content/70">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-2 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p>Este cidadão não tem requisições ativas.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Histórico de Requisições -->
            <div class="bg-base-100 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-base-content mb-4 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>

                        Histórico de Requisições ({{ $requisicoesPast->count() }})
                    </h3>

                    @if($requisicoesPast->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="table table-zebra w-full">
                                <thead>
                                    <tr>
                                        <th>Livro</th>
                                        <th>Foto na Requisição</th>
                                        <th>Estado</th>
                                        <th>Data Requisição</th>
                                        <th>Data Devolução</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($requisicoesPast as $requisicao)
                                        <tr>
                                            <td>
                                                <div class="flex items-center gap-3">
                                                    @if($requisicao->livro->imagem_capa)
                                                        <div class="avatar">
                                                            <div class="mask mask-squircle w-[50px] h-[50px]">
                                                                <img src="{{ $requisicao->livro->imagem_capa }}" alt="{{ $requisicao->livro->nome }}">
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <div class="font-medium">{{ $requisicao->livro->nome }}</div>
                                                        <div class="text-sm text-base-content/70">{{ $requisicao->livro->isbn }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($requisicao->foto_cidadao)
                                                    <div class="avatar">
                                                        <div class="w-[50px] rounded-full">
                                                            <img src="{{ $requisicao->foto_cidadao }}" alt="{{ $cidadao->name }}">
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-base-content/50 text-sm">Sem foto</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($requisicao->isDevolvida())
                                                    <span class="badge badge-success">Devolvido</span>
                                                @else
                                                    <span class="badge badge-error">Rejeitado</span>
                                                @endif
                                            </td>
                                            <td>{{ $requisicao->data_requisicao->format('d/m/Y') }}</td>
                                            <td>{{ $requisicao->data_devolucao?->format('d/m/Y') ?? 'N/D' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8 text-base-content/70">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-2 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p>Este cidadão não tem histórico de requisições.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
