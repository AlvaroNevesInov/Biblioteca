<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-base-content leading-tight">
                {{ __('Adicionar Novo Livro') }}
            </h2>
            <a href="{{ route('livros.index') }}" class="btn btn-ghost gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Voltar
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-base-100 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8">
                    <form action="{{ route('livros.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- ISBN -->
                            <div class="form-control w-full">
                                <label class="label">
                                    <span class="label-text font-semibold">ISBN <span class="text-error">*</span></span>
                                </label>
                                <input
                                    type="text"
                                    name="isbn"
                                    placeholder="978-9722034578"
                                    class="input input-bordered w-full @error('isbn') input-error @enderror"
                                    value="{{ old('isbn') }}"
                                    required
                                />
                                @error('isbn')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>

                            <!-- Nome do Livro -->
                            <div class="form-control w-full">
                                <label class="label">
                                    <span class="label-text font-semibold">Título do Livro <span class="text-error">*</span></span>
                                </label>
                                <input
                                    type="text"
                                    name="nome"
                                    placeholder="Ex: Ensaio sobre a Cegueira"
                                    class="input input-bordered w-full @error('nome') input-error @enderror"
                                    value="{{ old('nome') }}"
                                    required
                                />
                                @error('nome')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>

                            <!-- Editora -->
                            <div class="form-control w-full">
                                <label class="label">
                                    <span class="label-text font-semibold">Editora <span class="text-error">*</span></span>
                                </label>
                                <select
                                    name="editora_id"
                                    class="select select-bordered w-full @error('editora_id') select-error @enderror"
                                    required
                                >
                                    <option disabled selected>Selecione uma editora</option>
                                    @foreach($editoras as $editora)
                                        <option value="{{ $editora->id }}" {{ old('editora_id') == $editora->id ? 'selected' : '' }}>
                                            {{ $editora->nome }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('editora_id')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>

                            <!-- Preço -->
                            <div class="form-control w-full">
                                <label class="label">
                                    <span class="label-text font-semibold">Preço (€)</span>
                                </label>
                                <input
                                    type="number"
                                    name="preco"
                                    step="0.01"
                                    min="0"
                                    placeholder="15.99"
                                    class="input input-bordered w-full @error('preco') input-error @enderror"
                                    value="{{ old('preco') }}"
                                />
                                @error('preco')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>

                            <!-- Autores (ocupa 2 colunas) -->
                            <div class="form-control w-full md:col-span-2">
                                <label class="label">
                                    <span class="label-text font-semibold">Autores <span class="text-error">*</span></span>
                                    <span class="label-text-alt">Selecione um ou mais autores</span>
                                </label>
                                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 p-4 border border-base-300 rounded-lg max-h-60 overflow-y-auto">
                                    @foreach($autores as $autor)
                                        <div class="form-control">
                                            <label class="label cursor-pointer justify-start gap-2">
                                                <input
                                                    type="checkbox"
                                                    name="autores[]"
                                                    value="{{ $autor->id }}"
                                                    class="checkbox checkbox-primary checkbox-sm"
                                                    {{ in_array($autor->id, old('autores', [])) ? 'checked' : '' }}
                                                />
                                                <span class="label-text">{{ $autor->nome }}</span>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                @error('autores')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                @enderror
                                @error('autores.*')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>

                            <!-- Bibliografia (ocupa 2 colunas) -->
                            <div class="form-control w-full md:col-span-2">
                                <label class="label">
                                    <span class="label-text font-semibold">Bibliografia</span>
                                </label>
                                <textarea
                                    name="bibliografia"
                                    rows="4"
                                    placeholder="Breve descrição ou sinopse do livro..."
                                    class="textarea textarea-bordered w-full @error('bibliografia') textarea-error @enderror"
                                >{{ old('bibliografia') }}</textarea>
                                @error('bibliografia')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>

                            <!-- Imagem de Capa -->
                            <div class="form-control w-full md:col-span-2">
                                <label class="label">
                                    <span class="label-text font-semibold">Imagem de Capa</span>
                                    <span class="label-text-alt">Formatos: JPG, PNG (máx. 2MB)</span>
                                </label>
                                <input
                                    type="file"
                                    name="imagem_capa"
                                    accept="image/*"
                                    class="file-input file-input-bordered w-full @error('imagem_capa') file-input-error @enderror"
                                />
                                @error('imagem_capa')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>
                        </div>

                        <!-- Botões de Ação -->
                        <div class="flex justify-end gap-3 mt-8 pt-6 border-t border-base-300">
                            <a href="{{ route('livros.index') }}" class="btn btn-ghost">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Criar Livro
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
