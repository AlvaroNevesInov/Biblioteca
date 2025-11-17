<x-guest-layout>
    <div class="min-h-screen flex flex-col items-center justify-center bg-gray-100">
        <div class="max-w-md w-full bg-white shadow-lg rounded-lg p-8 text-center">
            <div class="mb-6">
                <svg class="mx-auto h-24 w-24 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>

            <h1 class="text-3xl font-bold text-gray-800 mb-4">Acesso Negado</h1>

            <p class="text-gray-600 mb-6">
                Lamentamos, mas não tem permissão para aceder a esta página.
                Esta área está restrita a administradores.
            </p>

            <div class="space-y-3">
                <a href="{{ route('dashboard') }}" class="block w-full btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Voltar ao Dashboard
                </a>

                <a href="{{ route('livros.index') }}" class="block w-full btn btn-ghost">
                    Ver Catálogo de Livros
                </a>
            </div>

            <div class="mt-6 pt-6 border-t border-gray-200">
                <p class="text-sm text-gray-500">
                    Se acha que deveria ter acesso a esta área, contacte um administrador.
                </p>
            </div>
        </div>
    </div>
</x-guest-layout>
