<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-base-content leading-tight">
                {{ __('Nova Requisição') }}
            </h2>
            <a href="{{ route('requisicoes.index') }}" class="btn btn-ghost gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Voltar
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if (session('error'))
                <div class="alert alert-error mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-base-100 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8">
                    <div class="alert alert-info mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h3 class="font-bold">Informação sobre Requisições</h3>
                            <div class="text-xs">
                                • Apenas livros disponíveis podem ser requisitados<br>
                                • A sua requisição será analisada por um administrador<br>
                                • Prazo padrão de devolução: <strong>5 dias</strong><br>
                                • Limite máximo de livros em simultâneo: <strong>3 livros</strong>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('requisicoes.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                             <!-- Seleção de Livro (apenas se não vier com livro pré-selecionado) -->

                        @if(!isset($livro))
                            <div class="form-control w-full mb-6">
                                <label class="label">
                                    <span class="label-text font-semibold">Selecione o Livro <span class="text-error">*</span></span>
                                    <span class="label-text-alt">{{ $livros->count() }} livros disponíveis</span>
                                </label>
                                <select
                                    name="livro_id"
                                    class="select select-bordered w-full @error('livro_id') select-error @enderror"
                                    required
                                >
                                    <option disabled selected>Escolha um livro</option>
                                    @foreach($livros as $l)
                                        <option
                                            value="{{ $l->id }}"
                                            {{ old('livro_id') == $l->id ? 'selected' : '' }}
                                        >
                                            {{ $l->nome }} ({{ $l->isbn }}) - {{ $l->editora->nome ?? 'N/A' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('livro_id')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>
                        @endif

                        <!-- Livro Selecionado (se vier da página de livros) -->

                        @if(isset($livro))

                            <input type="hidden" name="livro_id" value="{{ $livro->id }}">
                            <div class="card bg-base-200 mb-6">
                                <div class="card-body">
                                    <h3 class="card-title text-lg">Livro Selecionado</h3>
                                    <div class="flex gap-4">
                                        @if($livro->imagem_capa)
                                            <img src="{{ asset('storage/' . $livro->imagem_capa) }}" alt="{{ $livro->nome }}" class="h-32 w-24 object-cover rounded shadow">
                                        @endif
                                        <div class="flex-1">
                                            <p class="font-bold">{{ $livro->nome }}</p>
                                            <p class="text-sm text-base-content/60">ISBN: {{ $livro->isbn }}</p>
                                            <p class="text-sm text-base-content/60">Editora: {{ $livro->editora->nome ?? 'N/A' }}</p>
                                            <p class="text-sm text-base-content/60">
                                                Autores: {{ $livro->autores->pluck('nome')->join(', ') ?: 'Sem autor' }}
                                            </p>
                                            @if($livro->preco)
                                                <p class="text-sm font-semibold mt-2">Preço: € {{ number_format($livro->preco, 2, ',', '.') }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif


                        <!-- Foto do Cidadão -->

                        <div class="form-control w-full mb-6">
                            <label class="label">
                                <span class="label-text font-semibold">Foto de Identificação <span class="text-error">*</span></span>
                                <span class="label-text-alt">Obrigatório</span>
                            </label>
                            <input

                                type="file"
                                name="foto_cidadao"
                                accept="image/*"
                                class="file-input file-input-bordered w-full @error('foto_cidadao') file-input-error @enderror"
                                required
                            >

                            @error('foto_cidadao')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                            <label class="label">
                                <span class="label-text-alt text-base-content/60">Envie uma foto sua para registo da requisição</span>
                            </label>
                        </div>

                        <!-- Observações -->
                        <div class="form-control w-full mb-6">
                            <label class="label">
                                <span class="label-text font-semibold">Observações</span>
                                <span class="label-text-alt">Opcional</span>
                            </label>
                            <textarea
                                name="observacoes"
                                rows="3"
                                placeholder="Adicione alguma observação sobre esta requisição..."
                                class="textarea textarea-bordered w-full @error('observacoes') textarea-error @enderror"
                            >{{ old('observacoes') }}</textarea>
                            @error('observacoes')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <!-- Botões -->
                        <div class="flex justify-end gap-3 pt-4 border-t border-base-300">
                            <a href="{{ route('requisicoes.index') }}" class="btn btn-ghost">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Criar Requisição
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
