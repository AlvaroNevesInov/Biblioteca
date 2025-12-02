<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-base-content leading-tight">
                {{ __('Detalhes do Review') }}
            </h2>

            <a href="{{ route('reviews.index') }}" class="btn btn-ghost btn-sm gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Voltar
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="alert alert-success">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-error">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Informações do Review -->
            <div class="bg-base-100 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <div class="space-y-6">
                        <!-- Estado -->
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-2xl font-bold text-base-content">Review do Livro</h3>
                                <p class="text-lg text-base-content mt-2">{{ $review->livro->nome }}</p>
                            </div>
                            <div>
                                @if($review->isSuspenso())
                                    <div class="badge badge-warning badge-lg">Suspenso</div>
                                @elseif($review->isAtivo())
                                    <div class="badge badge-success badge-lg">Ativo</div>
                                @else
                                    <div class="badge badge-error badge-lg">Recusado</div>
                                @endif
                            </div>
                        </div>

                        <div class="divider"></div>

                        <!-- Informações do Cidadão -->
                        <div>
                            <h4 class="text-lg font-semibold text-base-content mb-3">Cidadão</h4>
                            <div class="flex items-center gap-4 p-4 bg-base-200 rounded-lg">
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
                                <div>
                                    <p class="font-semibold text-base-content">{{ $review->user->name }}</p>
                                    <p class="text-sm text-base-content/70">{{ $review->user->email }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Comentário -->
                        <div>
                            <h4 class="text-lg font-semibold text-base-content mb-3">Comentário</h4>
                            <div class="p-4 bg-base-200 rounded-lg">
                                <p class="text-base-content whitespace-pre-wrap">{{ $review->comentario }}</p>
                            </div>
                        </div>

                        <!-- Data de Submissão -->
                        <div>
                            <span class="text-sm font-medium text-base-content/70">Data de Submissão:</span>
                            <p class="text-base-content">{{ $review->created_at->format('d/m/Y H:i') }}</p>
                        </div>

                        @if($review->isRecusado() && $review->justificacao_recusa)
                            <!-- Justificação da Recusa -->
                            <div>
                                <h4 class="text-lg font-semibold text-base-content mb-3">Justificação da Recusa</h4>
                                <div class="p-4 bg-error/10 border border-error/20 rounded-lg">
                                    <p class="text-base-content whitespace-pre-wrap">{{ $review->justificacao_recusa }}</p>
                                </div>
                            </div>
                        @endif

                        <!-- Ações -->
                        @if($review->isSuspenso())
                            <div class="divider"></div>
                            <div class="flex gap-4">
                                <!-- Aprovar Review -->
                                <form action="{{ route('reviews.aprovar', $review) }}" method="POST" class="flex-1">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-block gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Aprovar Review
                                    </button>
                                </form>

                                <!-- Recusar Review -->
                                <button onclick="modal_recusar.showModal()" class="btn btn-error btn-block gap-2 flex-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Recusar Review
                                </button>
                            </div>

                            <!-- Modal de Recusa -->
                            <dialog id="modal_recusar" class="modal">
                                <div class="modal-box">
                                    <h3 class="font-bold text-lg">Recusar Review</h3>
                                    <form action="{{ route('reviews.recusar', $review) }}" method="POST" class="py-4">
                                        @csrf
                                        <div class="form-control">
                                            <label class="label">
                                                <span class="label-text">Justificação da Recusa *</span>
                                            </label>
                                            <textarea name="justificacao_recusa"
                                                      class="textarea textarea-bordered h-24"
                                                      placeholder="Explique o motivo da recusa do review..."
                                                      required
                                                      minlength="10"
                                                      maxlength="500"></textarea>
                                            <label class="label">
                                                <span class="label-text-alt">Mínimo 10 caracteres</span>
                                            </label>
                                        </div>

                                        <div class="modal-action">
                                            <button type="button" class="btn" onclick="modal_recusar.close()">Cancelar</button>
                                            <button type="submit" class="btn btn-error">Confirmar Recusa</button>
                                        </div>
                                    </form>
                                </div>
                                <form method="dialog" class="modal-backdrop">
                                    <button>fechar</button>
                                </form>
                            </dialog>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
