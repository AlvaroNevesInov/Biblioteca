<div class="">
    <!-- Tabs para alternar entre Catálogo Local e Google Books -->
    <div role="tablist" class="tabs tabs-boxed mb-4">
        <a wire:click="switchToLocal"
           role="tab"
           class="tab <?php echo e($searchMode === 'local' ? 'tab-active' : ''); ?>">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
            </svg>
            Catálogo Local
        </a>
        <a wire:click="switchToApi"
           role="tab"
           class="tab <?php echo e($searchMode === 'api' ? 'tab-active' : ''); ?>">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            Pesquisar Google Books
        </a>
    </div>

    <!-- Filtros e Pesquisa -->
    <div class="flex justify-between mb-4 gap-4">
        <div class="flex-1">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="<?php echo e($searchMode === 'local' ? 'Pesquisar por nome ou ISBN no catálogo local...' : 'Pesquisar livros na Google Books...'); ?>"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            >
        </div>

        <!--[if BLOCK]><![endif]--><?php if($searchMode === 'local'): ?>
            <div class="w-48">
                <select wire:model.live="filterEditora" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Todas as Editoras</option>
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $editoras; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $editora): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($editora->id); ?>"><?php echo e($editora->nome); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                </select>
            </div>
            <div class="w-32">
                <select wire:model.live="perPage" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="5">5 por página</option>
                    <option value="10">10 por página</option>
                    <option value="25">25 por página</option>
                    <option value="50">50 por página</option>
                    <option value="100">100 por página</option>
                </select>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        <!--[if BLOCK]><![endif]--><?php if($this->isAdmin && $searchMode === 'local'): ?>
        <div>
            <a href="<?php echo e(route('livros.create')); ?>" class="btn btn-primary gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Novo Livro
            </a>
        </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div>

    <!--[if BLOCK]><![endif]--><?php if($searchMode === 'api'): ?>
        <!-- Informação sobre modo API -->
        <div class="alert alert-info mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <h3 class="font-bold">Pesquisa na Google Books API</h3>
                <div class="text-sm">
                    Digite um termo para pesquisar. Ao requisitar um livro, ele será automaticamente importado para o catálogo.
                </div>
            </div>
        </div>

        <!-- Resultados da API -->
        <!--[if BLOCK]><![endif]--><?php if(!empty($search)): ?>
            <!--[if BLOCK]><![endif]--><?php if(!empty($apiResults)): ?>
                <div class="mb-4">
                    <div class="text-sm text-gray-700 mb-3">
                        Encontrados <span class="font-medium"><?php echo e(number_format($apiTotalResults)); ?></span> resultados para "<span class="font-medium"><?php echo e($search); ?></span>"
                    </div>
                </div>

                <div class="space-y-4">
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $apiResults; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $book): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="card lg:card-side bg-base-200 shadow-xl">
                            <figure class="lg:w-48">
                                <!--[if BLOCK]><![endif]--><?php if($book['thumbnail']): ?>
                                    <img src="<?php echo e($book['thumbnail']); ?>" alt="<?php echo e($book['title']); ?>" class="w-[50px] h-[50px] object-cover">
                                <?php else: ?>
                                    <div class="w-full h-full bg-base-300 flex items-center justify-center p-4">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                        </svg>
                                    </div>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </figure>
                            <div class="card-body">
                                <h2 class="card-title">
                                    <?php echo e($book['title']); ?>

                                    <!--[if BLOCK]><![endif]--><?php if($book['isbn']): ?>
                                        <div class="badge badge-primary">ISBN</div>
                                    <?php else: ?>
                                        <div class="badge badge-error">Sem ISBN</div>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </h2>

                                <!--[if BLOCK]><![endif]--><?php if($book['subtitle']): ?>
                                    <p class="text-sm opacity-70"><?php echo e($book['subtitle']); ?></p>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                <div class="space-y-1 text-sm">
                                    <!--[if BLOCK]><![endif]--><?php if(!empty($book['authors'])): ?>
                                        <p><strong>Autor(es):</strong> <?php echo e(implode(', ', $book['authors'])); ?></p>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                    <!--[if BLOCK]><![endif]--><?php if($book['publisher']): ?>
                                        <p><strong>Editora:</strong> <?php echo e($book['publisher']); ?></p>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                    <!--[if BLOCK]><![endif]--><?php if($book['published_date']): ?>
                                        <p><strong>Data de Publicação:</strong> <?php echo e($book['published_date']); ?></p>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                    <!--[if BLOCK]><![endif]--><?php if($book['isbn']): ?>
                                        <p><strong>ISBN:</strong> <?php echo e($book['isbn']); ?></p>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </div>

                                <!--[if BLOCK]><![endif]--><?php if($book['description']): ?>
                                    <p class="text-sm line-clamp-3"><?php echo e(Str::limit($book['description'], 200)); ?></p>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                <div class="card-actions justify-end mt-4">
                                    <!--[if BLOCK]><![endif]--><?php if($book['isbn']): ?>
                                        <?php
                                            $existingBook = \App\Models\Livro::where('isbn', $book['isbn'])->first();
                                        ?>

                                        <!--[if BLOCK]><![endif]--><?php if($existingBook): ?>
                                            <a href="<?php echo e(route('livros.show', $existingBook)); ?>" class="btn btn-sm btn-info">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                Ver no Catálogo
                                            </a>
                                            <!--[if BLOCK]><![endif]--><?php if($existingBook->estaDisponivel()): ?>
                                                <a href="<?php echo e(route('requisicoes.create', ['livro_id' => $existingBook->id])); ?>" class="btn btn-sm btn-success">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                    </svg>
                                                    Requisitar
                                                </a>
                                            <?php else: ?>
                                                <span class="btn btn-sm btn-disabled">Indisponível</span>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        <?php else: ?>
                                            <form action="<?php echo e(route('google-books.import-and-request')); ?>" method="POST" class="inline">
                                                <?php echo csrf_field(); ?>
                                                <input type="hidden" name="google_id" value="<?php echo e($book['google_id']); ?>">
                                                <button type="submit" class="btn btn-sm btn-success gap-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-[50px] w-[50px]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                    </svg>
                                                    Importar e Requisitar
                                                </button>
                                            </form>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    <?php else: ?>
                                        <span class="badge badge-error">Não pode ser importado (sem ISBN)</span>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                </div>

                <!-- Paginação API -->
                <div class="mt-6 flex justify-center">
                    <div class="join">
                        <!--[if BLOCK]><![endif]--><?php if($apiCurrentPage > 1): ?>
                            <button wire:click="previousApiPage" class="join-item btn">«</button>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                        <button class="join-item btn btn-active">Página <?php echo e($apiCurrentPage); ?></button>

                        <!--[if BLOCK]><![endif]--><?php if(($apiCurrentPage * 20) < $apiTotalResults): ?>
                            <button wire:click="nextApiPage" class="join-item btn">»</button>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-warning">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span>Nenhum resultado encontrado para "<?php echo e($search); ?>".</span>
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        <?php else: ?>
            <div class="alert">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Digite um termo de pesquisa para buscar livros na Google Books.</span>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <?php else: ?>
        <!-- Tabela de Livros Locais -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th wire:click="sortBy('isbn')" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 w-32">
                            ISBN
                            <!--[if BLOCK]><![endif]--><?php if($sortField === 'isbn'): ?>
                                <span><?php echo $sortDirection === 'asc' ? '&#8593;' : '&#8595;'; ?></span>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </th>
                        <th wire:click="sortBy('nome')" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 min-w-[200px]">
                            Nome
                            <!--[if BLOCK]><![endif]--><?php if($sortField === 'nome'): ?>
                                <span><?php echo $sortDirection === 'asc' ? '&#8593;' : '&#8595;'; ?></span>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                            Editora
                        </th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[150px]">
                            Autores
                        </th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[200px]">
                            Bibliografia
                        </th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-24">
                            Capa
                        </th>
                        <th wire:click="sortBy('preco')" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 w-24">
                            Preço
                            <!--[if BLOCK]><![endif]--><?php if($sortField === 'preco'): ?>
                                <span><?php echo $sortDirection === 'asc' ? '&#8593;' : '&#8595;'; ?></span>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                            Disponibilidade
                        </th>
                        <!--[if BLOCK]><![endif]--><?php if($this->isAdmin): ?>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-72">
                            Ações
                        </th>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $livros; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $livro): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                <?php echo e($livro->isbn); ?>

                            </td>
                            <td class="px-4 py-4 text-sm font-medium text-gray-900 text-center">
                                <a href="<?php echo e(route('livros.show', $livro)); ?>" class="text-blue-600 hover:text-blue-800 hover:underline">
                                    <?php echo e($livro->nome); ?>

                                </a>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                <?php echo e($livro->editora->nome ?? 'N/A'); ?>

                            </td>
                            <td class="px-4 py-4 text-sm text-gray-500 text-center">
                                <?php echo e($livro->autores->pluck('nome')->join(', ') ?: 'Sem autor'); ?>

                            </td>
                            <td class="px-4 py-4 text-sm text-gray-500 text-center" title="<?php echo e($livro->bibliografia); ?>">
                                <?php echo e(Str::limit($livro->bibliografia, 50, '...')); ?>

                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-center">
                                <div class="flex justify-center">
                                    <!--[if BLOCK]><![endif]--><?php if($livro->imagem_capa): ?>
                                        <img src="<?php echo e(asset('storage/' . $livro->imagem_capa)); ?>" alt="Capa de <?php echo e($livro->nome); ?>" class="h-16 w-12 object-cover rounded shadow-sm" loading="lazy">
                                    <?php else: ?>
                                        <span class="text-gray-400 text-xs">Sem imagem</span>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                <?php echo e($livro->preco ? '€ ' . number_format($livro->preco, 2, ',', '.') : 'N/A'); ?>

                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-center">
                                <div class="flex justify-center gap-2">
                                    <!--[if BLOCK]><![endif]--><?php if($livro->estaDisponivel()): ?>
                                        <a href="<?php echo e(route('requisicoes.create', ['livro_id' => $livro->id])); ?>"
                                        class="inline-flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded shadow-sm transition"
                                        title="Requisitar Livro">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                            </svg>
                                            Requisitar
                                        </a>
                                    <?php else: ?>
                                        <span class="btn btn-error btn-sm cursor-not-allowed"
                                            title="Livro não disponível">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                            </svg>
                                            Indisponível
                                        </span>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </td>
                            <!--[if BLOCK]><![endif]--><?php if($this->isAdmin): ?>
                                <td class="px-4 py-4 whitespace-nowrap text-center">
                                    <div class="flex justify-center gap-2">
                                        <!-- Botão Editar -->
                                        <a href="<?php echo e(route('livros.edit', $livro->id)); ?>"
                                        class="inline-flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded shadow-sm transition"
                                        title="Editar">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Editar
                                        </a>

                                        <!-- Botão Eliminar -->
                                        <button wire:click="confirmDelete(<?php echo e($livro->id); ?>)"
                                                class="inline-flex items-center px-3 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded shadow-sm transition"
                                                title="Eliminar"
                                                type="button">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Eliminar
                                        </button>
                                    </div>
                                </td>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="<?php echo e($this->isAdmin ? 9 : 8); ?>" class="px-4 py-4 text-center text-gray-500">
                                Nenhum livro encontrado.
                            </td>
                        </tr>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </tbody>
            </table>
        </div>

        <div class="mt-4 flex items-center justify-between">
            <div class="text-sm text-gray-700">
                Mostrando <span class="font-medium"><?php echo e($livros->firstItem() ?? 0); ?></span> a <span class="font-medium"><?php echo e($livros->lastItem() ?? 0); ?></span> de <span class="font-medium"><?php echo e($livros->total()); ?></span> resultados
            </div>
            <div>
                <?php echo e($livros->links()); ?>

            </div>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <!--[if BLOCK]><![endif]--><?php if($confirmingDelete): ?>
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Confirmar Eliminação</h3>
            <p class="text-gray-600 mb-6">
                Tem a certeza que deseja eliminar este livro? Esta ação não pode ser revertida.
            </p>
            <div class="flex justify-end gap-3">
                <button
                    wire:click="$set('confirmingDelete', false)"
                    class="btn btn-ghost"
                >
                    Cancelar
                </button>
                <button
                    wire:click="delete"
                    class="btn btn-error"
                >
                    Eliminar
                </button>
            </div>
        </div>
    </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
</div>
<?php /**PATH C:\Users\Utilizador\Herd\biblioteca\resources\views/livewire/livros-table.blade.php ENDPATH**/ ?>