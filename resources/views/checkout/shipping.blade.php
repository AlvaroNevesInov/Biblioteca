<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-base-content leading-tight">
                Checkout - Morada de Entrega
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Breadcrumb -->
            <div class="text-sm breadcrumbs mb-6">
                <ul>
                    <li><a href="{{ route('carrinho.index') }}">Carrinho</a></li>
                    <li class="font-bold">Morada de Entrega</li>
                    <li class="text-base-content/50">Pagamento</li>
                </ul>
            </div>

            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h3 class="card-title text-2xl mb-4">Informações de Entrega</h3>

                    <form method="POST" action="{{ route('checkout.shipping.process') }}">
                        @csrf

                        <!-- Nome Completo -->
                        <div class="form-control w-full">
                            <label class="label">
                                <span class="label-text">Nome Completo <span class="text-error">*</span></span>
                            </label>
                            <input type="text" name="nome_completo" value="{{ old('nome_completo', auth()->user()->name) }}" class="input input-bordered w-full @error('nome_completo') input-error @enderror" required>
                            @error('nome_completo')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="form-control w-full mt-4">
                            <label class="label">
                                <span class="label-text">Email <span class="text-error">*</span></span>
                            </label>
                            <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" class="input input-bordered w-full @error('email') input-error @enderror" required>
                            @error('email')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <!-- Telefone -->
                        <div class="form-control w-full mt-4">
                            <label class="label">
                                <span class="label-text">Telefone</span>
                            </label>
                            <input type="tel" name="telefone" value="{{ old('telefone') }}" class="input input-bordered w-full @error('telefone') input-error @enderror">
                            @error('telefone')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <!-- Morada -->
                        <div class="form-control w-full mt-4">
                            <label class="label">
                                <span class="label-text">Morada Completa <span class="text-error">*</span></span>
                            </label>
                            <textarea name="morada" rows="3" class="textarea textarea-bordered w-full @error('morada') textarea-error @enderror" required>{{ old('morada') }}</textarea>
                            @error('morada')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <!-- Cidade e Código Postal -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <!-- Cidade -->
                            <div class="form-control w-full">
                                <label class="label">
                                    <span class="label-text">Cidade <span class="text-error">*</span></span>
                                </label>
                                <input type="text" name="cidade" value="{{ old('cidade') }}" class="input input-bordered w-full @error('cidade') input-error @enderror" required>
                                @error('cidade')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>

                            <!-- Código Postal -->
                            <div class="form-control w-full">
                                <label class="label">
                                    <span class="label-text">Código Postal <span class="text-error">*</span></span>
                                </label>
                                <input type="text" name="codigo_postal" value="{{ old('codigo_postal') }}" placeholder="0000-000" class="input input-bordered w-full @error('codigo_postal') input-error @enderror" required>
                                @error('codigo_postal')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>
                        </div>

                        <!-- País -->
                        <div class="form-control w-full mt-4">
                            <label class="label">
                                <span class="label-text">País <span class="text-error">*</span></span>
                            </label>
                            <input type="text" name="pais" value="{{ old('pais', 'Portugal') }}" class="input input-bordered w-full @error('pais') input-error @enderror" required>
                            @error('pais')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <!-- Resumo da Encomenda -->
                        <div class="divider my-6"></div>

                        <h4 class="font-bold text-lg mb-4">Resumo da Encomenda</h4>

                        <div class="bg-base-200 p-4 rounded-lg space-y-2">
                            <div class="flex justify-between">
                                <span>Items no Carrinho:</span>
                                <span class="font-bold">{{ $carrinhoItems->count() }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Subtotal:</span>
                                <span class="font-bold">€{{ number_format($subtotal, 2) }}</span>
                            </div>
                        </div>

                        <!-- Botões -->
                        <div class="card-actions justify-between mt-6">
                            <a href="{{ route('carrinho.index') }}" class="btn btn-ghost">
                                Voltar ao Carrinho
                            </a>
                            <button type="submit" class="btn btn-primary">
                                Continuar para Pagamento
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
