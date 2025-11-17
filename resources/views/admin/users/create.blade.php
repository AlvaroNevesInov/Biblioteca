<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-base-content leading-tight">
                {{ __('Criar Novo Utilizador') }}
            </h2>
            <a href="{{ route('admin.users.index') }}" class="btn btn-ghost gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Voltar
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-base-100 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8">
                    <div class="alert alert-info mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Apenas administradores podem criar outros administradores. O utilizador receberá as credenciais por email.</span>
                    </div>

                    <form action="{{ route('admin.users.store') }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nome -->
                            <div class="form-control w-full">
                                <label class="label">
                                    <span class="label-text font-semibold">Nome <span class="text-error">*</span></span>
                                </label>
                                <input
                                    type="text"
                                    name="name"
                                    placeholder="Ex: João Silva"
                                    class="input input-bordered w-full @error('name') input-error @enderror"
                                    value="{{ old('name') }}"
                                    required
                                />
                                @error('name')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="form-control w-full">
                                <label class="label">
                                    <span class="label-text font-semibold">Email <span class="text-error">*</span></span>
                                </label>
                                <input
                                    type="email"
                                    name="email"
                                    placeholder="exemplo@email.com"
                                    class="input input-bordered w-full @error('email') input-error @enderror"
                                    value="{{ old('email') }}"
                                    required
                                />
                                @error('email')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>

                            <input type="hidden" name="role" value="admin">

                            <!-- Password -->
                            <div class="form-control w-full">
                                <label class="label">
                                    <span class="label-text font-semibold">Password <span class="text-error">*</span></span>
                                </label>
                                <input
                                    type="password"
                                    name="password"
                                    placeholder="••••••••"
                                    class="input input-bordered w-full @error('password') input-error @enderror"
                                    required
                                />
                                @error('password')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div class="form-control w-full">
                                <label class="label">
                                    <span class="label-text font-semibold">Confirmar Password <span class="text-error">*</span></span>
                                </label>
                                <input
                                    type="password"
                                    name="password_confirmation"
                                    placeholder="••••••••"
                                    class="input input-bordered w-full"
                                    required
                                />
                            </div>
                        </div>

                        <!-- Botões de Ação -->
                        <div class="flex justify-end gap-3 mt-8 pt-6 border-t border-base-300">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-ghost">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                </svg>
                                Criar Utilizador
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
