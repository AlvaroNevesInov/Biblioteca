<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard') }}
            </h2>

            @if(auth()->user()->isAdmin())
                <span class="badge badge-error gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-4 h-4 stroke-current">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    Administrador
                </span>
            @else
                <span class="badge badge-info gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-4 h-4 stroke-current">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Cidadão
                </span>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(auth()->user()->isAdmin())
                {{-- Dashboard para ADMIN --}}
                <div class="mb-6">
                    <div class="alert alert-success">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h3 class="font-bold">Bem-vindo, Administrador!</h3>
                            <div class="text-xs">Tem acesso total à gestão da biblioteca</div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Card Autores -->
                    <a href="{{ route('autores.index') }}" class="block">
                        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg hover:shadow-2xl transition-shadow duration-300 transform hover:scale-105">
                            <div class="p-8 text-center">
                                <div class="mb-4">
                                    <svg class="w-20 h-20 mx-auto text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <h3 class="text-2xl font-bold text-gray-800 mb-2">Autores</h3>
                                <p class="text-gray-600">Gerir autores de livros</p>
                            </div>
                        </div>
                    </a>

                    <!-- Card Livros -->
                    <a href="{{ route('livros.index') }}" class="block">
                        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg hover:shadow-2xl transition-shadow duration-300 transform hover:scale-105">
                            <div class="p-8 text-center">
                                <div class="mb-4">
                                    <svg class="w-20 h-20 mx-auto text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                </div>
                                <h3 class="text-2xl font-bold text-gray-800 mb-2">Livros</h3>
                                <p class="text-gray-600">Gerir catálogo de livros</p>
                            </div>
                        </div>
                    </a>

                    <!-- Card Editoras -->
                    <a href="{{ route('editoras.index') }}" class="block">
                        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg hover:shadow-2xl transition-shadow duration-300 transform hover:scale-105">
                            <div class="p-8 text-center">
                                <div class="mb-4">
                                    <svg class="w-20 h-20 mx-auto text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                                <h3 class="text-2xl font-bold text-gray-800 mb-2">Editoras</h3>
                                <p class="text-gray-600">Gerir editoras</p>
                            </div>
                        </div>
                    </a>
                </div>

            @else
                {{-- Dashboard para CIDADÃO --}}
                <div class="mb-6">
                    <div class="alert alert-info">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h3 class="font-bold">Bem-vindo à Biblioteca!</h3>
                            <div class="text-xs">Explore o nosso catálogo e descubra novos livros</div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Card Explorar Catálogo -->
                    <a href="{{ route('livros.index') }}" class="block">
                        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg hover:shadow-2xl transition-shadow duration-300 transform hover:scale-105">
                            <div class="p-8 text-center">
                                <div class="mb-4">
                                    <svg class="w-20 h-20 mx-auto text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                </div>
                                <h3 class="text-2xl font-bold text-gray-800 mb-2">Explorar Catálogo</h3>
                                <p class="text-gray-600">Descubra todos os livros disponíveis</p>
                            </div>
                        </div>
                    </a>

                    <!-- Card Ver Autores -->
                    <a href="{{ route('autores.index') }}" class="block">
                        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg hover:shadow-2xl transition-shadow duration-300 transform hover:scale-105">
                            <div class="p-8 text-center">
                                <div class="mb-4">
                                    <svg class="w-20 h-20 mx-auto text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <h3 class="text-2xl font-bold text-gray-800 mb-2">Ver Autores</h3>
                                <p class="text-gray-600">Conheça os autores da biblioteca</p>
                            </div>
                        </div>
                    </a>

                    <!-- Card Ver Editoras -->
                    <a href="{{ route('editoras.index') }}" class="block">
                        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg hover:shadow-2xl transition-shadow duration-300 transform hover:scale-105">
                            <div class="p-8 text-center">
                                <div class="mb-4">
                                    <svg class="w-20 h-20 mx-auto text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                                <h3 class="text-2xl font-bold text-gray-800 mb-2">Ver Editoras</h3>
                                <p class="text-gray-600">Conheça as editoras parceiras</p>
                            </div>
                        </div>
                    </a>
                </div>

                {{-- Info adicional para cidadãos --}}
                <div class="mt-8 bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800">Funcionalidades Disponíveis</h3>
                    <ul class="space-y-2">
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-gray-700">Explorar o catálogo completo de livros</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-gray-700">Pesquisar livros por título, ISBN ou editora</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-gray-700">Ver informações detalhadas de autores e editoras</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-yellow-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-gray-700">Em breve: Solicitar requisições de livros</span>
                        </li>
                    </ul>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
