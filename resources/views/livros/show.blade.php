<x-app-layout>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-base-content leading-tight">

                {{ __('Detalhes do Livro') }}

            </h2>

            <a href="{{ route('livros.index') }}" class="btn btn-ghost btn-sm gap-2">

                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">

                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />

                </svg>

                Voltar

            </a>
        </div>
    </x-slot>



    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Informações do Livro -->
            <div class="bg-base-100 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <div class="flex flex-col md:flex-row gap-6">
                        <!-- Capa do Livro -->
                        <div class="flex-shrink-0">
                            @if($livro->imagem_capa)

                                <img src="{{ asset('storage/' . $livro->imagem_capa) }}"

                                     alt="{{ $livro->nome }}"

                                     class="w-48 h-64 object-cover rounded-lg shadow-md">

                            @else

                                <div class="w-48 h-64 bg-base-300 rounded-lg flex items-center justify-center">

                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-base-content/30" fill="none" viewBox="0 0 24 24" stroke="currentColor">

                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />

                                    </svg>
                                </div>
                            @endif
                        </div>

                        <!-- Detalhes do Livro -->

                        <div class="flex-grow space-y-4">
                            <h3 class="text-2xl font-bold text-base-content">{{ $livro->nome }}</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                                <div>
                                    <span class="text-sm font-medium text-base-content/70">ISBN:</span>
                                    <p class="text-base-content font-mono">{{ $livro->isbn }}</p>
                                </div>

                                <div>
                                    <span class="text-sm font-medium text-base-content/70">Editora:</span>
                                    <p class="text-base-content">{{ $livro->editora->nome }}</p>
                                </div>

                                <div>
                                    <span class="text-sm font-medium text-base-content/70">Autores:</span>
                                    <p class="text-base-content">{{ $livro->autores->pluck('nome')->join(', ') }}</p>
                                </div>

                                <div>
                                    <span class="text-sm font-medium text-base-content/70">Preço:</span>
                                    <p class="text-base-content font-semibold">
                                        @if($livro->preco)

                                            {{ number_format($livro->preco, 2, ',', '.') }} €

                                        @else

                                            <span class="text-base-content/50">N/D</span>

                                        @endif
                                    </p>
                                </div>
                            </div>

                            @if($livro->bibliografia)
                                <div>
                                    <span class="text-sm font-medium text-base-content/70">Bibliografia:</span>
                                    <p class="text-base-content mt-1">{{ $livro->bibliografia }}</p>
                                </div>
                            @endif

                            <!-- Estado de Disponibilidade -->
                            <div class="pt-4">
                                @if($livro->estaDisponivel())
                                    <div class="badge badge-success gap-2">

                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">

                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />

                                        </svg>

                                        Disponível para requisição

                                    </div>
                                @else
                                    <div class="badge badge-warning gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>

                                        Indisponível

                                    </div>
                                @endif
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

                        Requisições Ativas
                    </h3>



                    @if($requisicoesAtivas->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="table table-zebra w-full">
                                <thead>
                                    <tr>
                                        <th>Cidadão</th>
                                        <th>Foto</th>
                                        <th>Estado</th>
                                        <th>Data Requisição</th>
                                        <th>Data Prevista Devolução</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($requisicoesAtivas as $requisicao)
                                        <tr>
                                            <td>
                                                <div class="font-medium">{{ $requisicao->user->name }}</div>
                                                <div class="text-sm text-base-content/70">{{ $requisicao->user->email }}</div>
                                            </td>
                                            <td>
                                                @if($requisicao->foto_cidadao)
                                                    <div class="avatar">
                                                        <div class="w-[50px] rounded-full">
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

                            <p>Não existem requisições ativas para este livro.</p>
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

                        Histórico de Requisições
                    </h3>

                    @if($requisicoesPast->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="table table-zebra w-full">
                                <thead>
                                    <tr>
                                        <th>Cidadão</th>
                                        <th>Foto</th>
                                        <th>Estado</th>
                                        <th>Data Requisição</th>
                                        <th>Data Devolução</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($requisicoesPast as $requisicao)
                                        <tr>
                                            <td>
                                                <div class="font-medium">{{ $requisicao->user->name }}</div>
                                                <div class="text-sm text-base-content/70">{{ $requisicao->user->email }}</div>
                                            </td>
                                            <td>
                                                @if($requisicao->foto_cidadao)
                                                    <div class="avatar">
                                                        <div class="w-[50px] rounded-full">
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
                            <p>Não existe histórico de requisições para este livro.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Reviews Ativos -->
            <div class="bg-base-100 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-base-content mb-4 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                        </svg>
                        Reviews
                    </h3>

                    @php
                        $reviewsAtivos = $livro->reviewsAtivos()->get();
                    @endphp

                    @if($reviewsAtivos->count() > 0)
                        <div class="space-y-4">
                            @foreach($reviewsAtivos as $review)
                                <div class="bg-base-200 rounded-lg p-4">
                                    <div class="flex items-start gap-4">
                                        <div class="avatar">
                                            <div class="w-12 rounded-full">
                                                @if($review->user->profile_photo_path)
                                                    <img src="{{ $review->user->profile_photo_url }}" alt="{{ $review->user->name }}">
                                                @else
                                                    <div class="w-12 h-12 rounded-full bg-primary text-primary-content flex items-center justify-center font-bold">
                                                        {{ substr($review->user->name, 0, 1) }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between mb-2">
                                                <div>
                                                    <p class="font-semibold text-base-content">{{ $review->user->name }}</p>
                                                    <p class="text-sm text-base-content/70">{{ $review->created_at->format('d/m/Y') }}</p>
                                                </div>
                                            </div>
                                            <p class="text-base-content whitespace-pre-wrap">{{ $review->comentario }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-base-content/70">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-2 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                            </svg>
                            <p>Não existem reviews para este livro ainda.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
