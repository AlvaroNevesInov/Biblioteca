<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('header', null, []); ?> 
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-base-content leading-tight">
                Encomenda Confirmada
            </h2>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Mensagem de Sucesso -->
            <div class="alert alert-success mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-8 w-8" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <div>
                    <h3 class="font-bold text-lg">Pagamento Confirmado!</h3>
                    <div class="text-sm">Obrigado pela sua encomenda. Receberá um email de confirmação em breve.</div>
                </div>
            </div>

            <!-- Detalhes da Encomenda -->
            <div class="card bg-base-100 shadow-xl mb-6">
                <div class="card-body">
                    <h3 class="card-title text-2xl">Detalhes da Encomenda</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div>
                            <p class="text-sm text-base-content/70">Número da Encomenda</p>
                            <p class="font-bold text-lg"><?php echo e($encomenda->numero_encomenda); ?></p>
                        </div>

                        <div>
                            <p class="text-sm text-base-content/70">Data</p>
                            <p class="font-bold"><?php echo e($encomenda->created_at->format('d/m/Y H:i')); ?></p>
                        </div>

                        <div>
                            <p class="text-sm text-base-content/70">Estado</p>
                            <span class="badge badge-success"><?php echo e(ucfirst($encomenda->estado)); ?></span>
                        </div>

                        <div>
                            <p class="text-sm text-base-content/70">Total</p>
                            <p class="font-bold text-primary text-lg">€<?php echo e(number_format($encomenda->total, 2)); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Morada de Entrega -->
            <div class="card bg-base-100 shadow-xl mb-6">
                <div class="card-body">
                    <h3 class="card-title">Morada de Entrega</h3>
                    <div class="text-sm mt-2">
                        <p><strong><?php echo e($encomenda->nome_completo); ?></strong></p>
                        <p><?php echo e($encomenda->email); ?></p>
                        <?php if($encomenda->telefone): ?>
                            <p><?php echo e($encomenda->telefone); ?></p>
                        <?php endif; ?>
                        <p class="mt-2"><?php echo e($encomenda->morada); ?></p>
                        <p><?php echo e($encomenda->cidade); ?>, <?php echo e($encomenda->codigo_postal); ?></p>
                        <p><?php echo e($encomenda->pais); ?></p>
                    </div>
                </div>
            </div>

            <!-- Items da Encomenda -->
            <div class="card bg-base-100 shadow-xl mb-6">
                <div class="card-body">
                    <h3 class="card-title">Items da Encomenda</h3>

                    <div class="overflow-x-auto mt-4">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Livro</th>
                                    <th class="text-center">Quantidade</th>
                                    <th class="text-right">Preço Unitário</th>
                                    <th class="text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $encomenda->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td>
                                            <div class="flex items-center gap-3">
                                                <?php if($item->livro->imagem_capa): ?>
                                                    <div class="avatar">
                                                        <div class="mask mask-squircle w-12 h-12">
                                                            <img src="<?php echo e(Storage::url($item->livro->imagem_capa)); ?>" alt="<?php echo e($item->livro->nome); ?>" />
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                                <div>
                                                    <div class="font-bold"><?php echo e($item->livro->nome); ?></div>
                                                    <div class="text-sm opacity-50"><?php echo e($item->livro->autores->pluck('nome')->join(', ')); ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center"><?php echo e($item->quantidade); ?></td>
                                        <td class="text-right">€<?php echo e(number_format($item->preco_unitario, 2)); ?></td>
                                        <td class="text-right font-bold">€<?php echo e(number_format($item->subtotal, 2)); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-right">Subtotal</th>
                                    <th class="text-right">€<?php echo e(number_format($encomenda->subtotal, 2)); ?></th>
                                </tr>
                                <tr>
                                    <th colspan="3" class="text-right">Taxas</th>
                                    <th class="text-right">€<?php echo e(number_format($encomenda->taxas, 2)); ?></th>
                                </tr>
                                <tr>
                                    <th colspan="3" class="text-right text-lg">Total</th>
                                    <th class="text-right text-lg text-primary">€<?php echo e(number_format($encomenda->total, 2)); ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Botões de Ação -->
            <div class="flex justify-between">
                <a href="<?php echo e(route('livros.index')); ?>" class="btn btn-ghost">
                    Continuar a Comprar
                </a>
                <a href="<?php echo e(route('encomendas.show', $encomenda)); ?>" class="btn btn-primary">
                    Ver Detalhes da Encomenda
                </a>
            </div>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH C:\Users\Utilizador\Herd\biblioteca\resources\views/checkout/success.blade.php ENDPATH**/ ?>