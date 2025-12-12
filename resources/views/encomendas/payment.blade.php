<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-base-content leading-tight">
                Pagamento da Encomenda #{{ $encomenda->numero_encomenda }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            @if(session('error'))
                <div class="alert alert-error mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Voltar -->
            <div class="mb-6">
                <a href="{{ route('encomendas.show', $encomenda) }}" class="btn btn-ghost btn-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Voltar
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Formulário de Pagamento -->
                <div class="lg:col-span-2">
                    <div class="card bg-base-100 shadow-xl">
                        <div class="card-body">
                            <h3 class="card-title text-2xl mb-4">Informações de Pagamento</h3>

                            <div class="alert alert-info mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <div>
                                    <h3 class="font-bold">Pagamento Seguro com Stripe</h3>
                                    <div class="text-xs">Os seus dados de pagamento são processados de forma segura. Não armazenamos detalhes do cartão de crédito.</div>
                                    <div class="text-xs mt-1"><strong>Modo Teste:</strong> Use o cartão 4242 4242 4242 4242, qualquer data futura e qualquer CVC.</div>
                                </div>
                            </div>

                            <form id="payment-form">
                                @csrf

                                <!-- Stripe Card Element -->
                                <div class="form-control w-full mb-4">
                                    <label class="label">
                                        <span class="label-text">Detalhes do Cartão</span>
                                    </label>
                                    <div id="card-element" class="input input-bordered p-3 h-auto"></div>
                                    <div id="card-errors" role="alert" class="text-error text-sm mt-2"></div>
                                </div>

                                <div class="card-actions justify-between mt-6">
                                    <a href="{{ route('encomendas.show', $encomenda) }}" class="btn btn-ghost">
                                        Cancelar
                                    </a>
                                    <button type="submit" id="submit-button" class="btn btn-primary">
                                        <span id="button-text">Pagar €{{ number_format($encomenda->total, 2) }}</span>
                                        <span id="spinner" class="loading loading-spinner loading-sm hidden"></span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Morada de Entrega -->
                    <div class="card bg-base-100 shadow-xl mt-6">
                        <div class="card-body">
                            <h3 class="card-title">Morada de Entrega</h3>
                            <div class="text-sm">
                                <p><strong>{{ $encomenda->nome_completo }}</strong></p>
                                <p>{{ $encomenda->email }}</p>
                                @if($encomenda->telefone)
                                    <p>{{ $encomenda->telefone }}</p>
                                @endif
                                <p class="mt-2">{{ $encomenda->morada }}</p>
                                <p>{{ $encomenda->cidade }}, {{ $encomenda->codigo_postal }}</p>
                                <p>{{ $encomenda->pais }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Resumo da Encomenda -->
                <div class="lg:col-span-1">
                    <div class="card bg-base-100 shadow-xl sticky top-4">
                        <div class="card-body">
                            <h3 class="card-title">Resumo da Encomenda</h3>

                            <div class="space-y-2 mt-4">
                                @foreach($encomenda->items as $item)
                                    <div class="flex justify-between text-sm">
                                        <span>{{ $item->livro->nome }} (x{{ $item->quantidade }})</span>
                                        <span>€{{ number_format($item->subtotal, 2) }}</span>
                                    </div>
                                @endforeach

                                <div class="divider my-2"></div>

                                <div class="flex justify-between">
                                    <span>Subtotal</span>
                                    <span class="font-bold">€{{ number_format($encomenda->subtotal, 2) }}</span>
                                </div>

                                <div class="flex justify-between">
                                    <span>Taxas</span>
                                    <span class="font-bold">€{{ number_format($encomenda->taxas, 2) }}</span>
                                </div>

                                <div class="divider my-2"></div>

                                <div class="flex justify-between text-lg font-bold">
                                    <span>Total</span>
                                    <span class="text-primary">€{{ number_format($encomenda->total, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        // Inicializar Stripe
        const stripe = Stripe('{{ config('services.stripe.key') }}');
        const elements = stripe.elements();

        // Criar o Card Element
        const cardElement = elements.create('card', {
            style: {
                base: {
                    fontSize: '16px',
                    color: '#32325d',
                    fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
                    '::placeholder': {
                        color: '#a0aec0',
                    },
                },
            },
        });

        cardElement.mount('#card-element');

        // Mostrar erros
        cardElement.on('change', function(event) {
            const displayError = document.getElementById('card-errors');
            if (event.error) {
                displayError.textContent = event.error.message;
            } else {
                displayError.textContent = '';
            }
        });

        // Processar pagamento
        const form = document.getElementById('payment-form');
        const submitButton = document.getElementById('submit-button');
        const buttonText = document.getElementById('button-text');
        const spinner = document.getElementById('spinner');

        form.addEventListener('submit', async function(event) {
            event.preventDefault();

            // Desabilitar botão
            submitButton.disabled = true;
            buttonText.classList.add('hidden');
            spinner.classList.remove('hidden');

            try {
                // Confirmar pagamento
                const {paymentIntent, error} = await stripe.confirmCardPayment(
                    '{{ $paymentIntent->client_secret }}',
                    {
                        payment_method: {
                            card: cardElement,
                        }
                    }
                );

                if (error) {
                    // Mostrar erro
                    const displayError = document.getElementById('card-errors');
                    displayError.textContent = error.message;

                    // Reabilitar botão
                    submitButton.disabled = false;
                    buttonText.classList.remove('hidden');
                    spinner.classList.add('hidden');
                } else {
                    // Pagamento bem sucedido
                    if (paymentIntent.status === 'succeeded') {
                        // Enviar o payment intent ID para o servidor
                        const formData = new FormData();
                        formData.append('_token', '{{ csrf_token() }}');
                        formData.append('payment_intent_id', paymentIntent.id);

                        const response = await fetch('{{ route('encomendas.payment.process', $encomenda) }}', {
                            method: 'POST',
                            body: formData
                        });

                        if (response.ok) {
                            window.location.href = await response.text();
                        } else {
                            throw new Error('Erro ao processar o pagamento');
                        }
                    }
                }
            } catch (err) {
                console.error(err);
                alert('Ocorreu um erro ao processar o pagamento. Por favor, tente novamente.');

                // Reabilitar botão
                submitButton.disabled = false;
                buttonText.classList.remove('hidden');
                spinner.classList.add('hidden');
            }
        });
    </script>
    @endpush
</x-app-layout>
