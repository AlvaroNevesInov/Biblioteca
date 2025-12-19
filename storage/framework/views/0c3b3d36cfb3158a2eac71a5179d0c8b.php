<div class="">
    <!-- Filtros e Pesquisa -->
    <div class="mb-4 space-y-4">
        <!-- Linha 1: Pesquisa -->
        <div class="flex gap-4">
            <div class="flex-1">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Pesquisar por descrição, IP ou utilizador..."
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
            </div>
            <div class="w-48">
                <select wire:model.live="perPage" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="10">10 por página</option>
                    <option value="25">25 por página</option>
                    <option value="50">50 por página</option>
                    <option value="100">100 por página</option>
                </select>
            </div>
        </div>

        <!-- Linha 2: Filtros -->
        <div class="flex gap-4">
            <div class="flex-1">
                <select wire:model.live="filterModulo" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Todos os Módulos</option>
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $modulos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $modulo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($modulo); ?>"><?php echo e($modulo); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                </select>
            </div>
            <div class="flex-1">
                <select wire:model.live="filterAcao" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Todas as Ações</option>
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $acoes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $acao): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($acao); ?>"><?php echo e($acao); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                </select>
            </div>
            <div class="flex-1">
                <select wire:model.live="filterUserId" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Todos os Utilizadores</option>
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($user->id); ?>"><?php echo e($user->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                </select>
            </div>
        </div>

        <!-- Linha 3: Filtros de Data -->
        <div class="flex gap-4">
            <div class="flex-1">
                <input
                    type="date"
                    wire:model.live="dateFrom"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="Data inicial"
                >
            </div>
            <div class="flex-1">
                <input
                    type="date"
                    wire:model.live="dateTo"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="Data final"
                >
            </div>
            <div>
                <button wire:click="clearFilters" class="btn btn-ghost gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Limpar Filtros
                </button>
            </div>
        </div>
    </div>

    <!-- Tabela de Logs -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th wire:click="sortBy('id')" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 w-20">
                        ID
                        <!--[if BLOCK]><![endif]--><?php if($sortField === 'id'): ?>
                            <span><?php echo $sortDirection === 'asc' ? '&#8593;' : '&#8595;'; ?></span>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </th>
                    <th wire:click="sortBy('created_at')" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 w-40">
                        Data/Hora
                        <!--[if BLOCK]><![endif]--><?php if($sortField === 'created_at'): ?>
                            <span><?php echo $sortDirection === 'asc' ? '&#8593;' : '&#8595;'; ?></span>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-40">
                        Utilizador
                    </th>
                    <th wire:click="sortBy('modulo')" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 w-32">
                        Módulo
                        <!--[if BLOCK]><![endif]--><?php if($sortField === 'modulo'): ?>
                            <span><?php echo $sortDirection === 'asc' ? '&#8593;' : '&#8595;'; ?></span>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </th>
                    <th wire:click="sortBy('acao')" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 w-32">
                        Ação
                        <!--[if BLOCK]><![endif]--><?php if($sortField === 'acao'): ?>
                            <span><?php echo $sortDirection === 'asc' ? '&#8593;' : '&#8595;'; ?></span>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-24">
                        ID Objeto
                    </th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[250px]">
                        Descrição
                    </th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                        IP
                    </th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[200px]">
                        Browser
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 text-center">
                            <?php echo e($log->id); ?>

                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 text-center">
                            <div><?php echo e($log->created_at->format('d/m/Y')); ?></div>
                            <div class="text-xs text-gray-500"><?php echo e($log->created_at->format('H:i:s')); ?></div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 text-center">
                            <!--[if BLOCK]><![endif]--><?php if($log->user): ?>
                                <div class="flex items-center justify-center">
                                    <div>
                                        <div class="font-medium"><?php echo e($log->user->name); ?></div>
                                        <div class="text-xs text-gray-500"><?php echo e($log->user->email); ?></div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <span class="text-gray-400">Sistema</span>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-center">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                <?php echo e($log->modulo); ?>

                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-center">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                <?php echo e($log->acao === 'Criar' ? 'bg-green-100 text-green-800' : ''); ?>

                                <?php echo e($log->acao === 'Atualizar' ? 'bg-yellow-100 text-yellow-800' : ''); ?>

                                <?php echo e($log->acao === 'Eliminar' ? 'bg-red-100 text-red-800' : ''); ?>

                                <?php echo e($log->acao === 'Visualizar' ? 'bg-gray-100 text-gray-800' : ''); ?>

                                <?php echo e(!in_array($log->acao, ['Criar', 'Atualizar', 'Eliminar', 'Visualizar']) ? 'bg-purple-100 text-purple-800' : ''); ?>

                            ">
                                <?php echo e($log->acao); ?>

                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 text-center">
                            <?php echo e($log->object_id ?? '-'); ?>

                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500 text-center">
                            <!--[if BLOCK]><![endif]--><?php if($log->descricao): ?>
                                <div title="<?php echo e($log->descricao); ?>">
                                    <?php echo e(Str::limit($log->descricao, 60)); ?>

                                </div>
                            <?php else: ?>
                                <span class="text-gray-400">-</span>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 text-center font-mono text-xs">
                            <?php echo e($log->ip_address ?? '-'); ?>

                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500 text-center">
                            <!--[if BLOCK]><![endif]--><?php if($log->user_agent): ?>
                                <div title="<?php echo e($log->user_agent); ?>" class="text-xs">
                                    <?php echo e(Str::limit($log->user_agent, 40)); ?>

                                </div>
                            <?php else: ?>
                                <span class="text-gray-400">-</span>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="9" class="px-4 py-8 text-center text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="text-lg font-medium">Nenhum log encontrado</p>
                            <p class="text-sm">Não há registos que correspondam aos critérios de pesquisa.</p>
                        </td>
                    </tr>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </tbody>
        </table>
    </div>

    <!-- Paginação -->
    <div class="mt-4">
        <?php echo e($logs->links()); ?>

    </div>

    <!-- Estatísticas -->
    <div class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="stat bg-base-200 rounded-lg p-4">
            <div class="stat-title">Total de Logs</div>
            <div class="stat-value text-primary"><?php echo e($logs->total()); ?></div>
        </div>
        <div class="stat bg-base-200 rounded-lg p-4">
            <div class="stat-title">Página Atual</div>
            <div class="stat-value text-secondary"><?php echo e($logs->currentPage()); ?></div>
        </div>
        <div class="stat bg-base-200 rounded-lg p-4">
            <div class="stat-title">Total de Páginas</div>
            <div class="stat-value"><?php echo e($logs->lastPage()); ?></div>
        </div>
        <div class="stat bg-base-200 rounded-lg p-4">
            <div class="stat-title">Logs por Página</div>
            <div class="stat-value"><?php echo e($logs->perPage()); ?></div>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\Utilizador\Herd\biblioteca\resources\views/livewire/logs-table.blade.php ENDPATH**/ ?>