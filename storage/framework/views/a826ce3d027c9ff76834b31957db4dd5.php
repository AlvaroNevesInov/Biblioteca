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
                <?php echo e(__('Nova Requisição')); ?>

            </h2>
            <a href="<?php echo e(route('requisicoes.index')); ?>" class="btn btn-ghost gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Voltar
            </a>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <?php if(session('error')): ?>
                <div class="alert alert-error mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span><?php echo e(session('error')); ?></span>
                </div>
            <?php endif; ?>

            <div class="bg-base-100 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8">
                    <div class="alert alert-info mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h3 class="font-bold">Informação sobre Requisições</h3>
                            <div class="text-xs">
                                • Apenas livros disponíveis podem ser requisitados<br>
                                • A sua requisição será analisada por um administrador<br>
                                • Prazo padrão de devolução: <strong>5 dias</strong><br>
                                • Limite máximo de livros em simultâneo: <strong>3 livros</strong>
                            </div>
                        </div>
                    </div>

                    <form action="<?php echo e(route('requisicoes.store')); ?>" method="POST" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>

                             <!-- Seleção de Livro (apenas se não vier com livro pré-selecionado) -->

                        <?php if(!isset($livro)): ?>
                            <div class="form-control w-full mb-6">
                                <label class="label">
                                    <span class="label-text font-semibold">Selecione o Livro <span class="text-error">*</span></span>
                                    <span class="label-text-alt"><?php echo e($livros->count()); ?> livros disponíveis</span>
                                </label>
                                <select
                                    name="livro_id"
                                    class="select select-bordered w-full <?php $__errorArgs = ['livro_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> select-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    required
                                >
                                    <option disabled selected>Escolha um livro</option>
                                    <?php $__currentLoopData = $livros; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option
                                            value="<?php echo e($l->id); ?>"
                                            <?php echo e(old('livro_id') == $l->id ? 'selected' : ''); ?>

                                        >
                                            <?php echo e($l->nome); ?> (<?php echo e($l->isbn); ?>) - <?php echo e($l->editora->nome ?? 'N/A'); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['livro_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <label class="label">
                                        <span class="label-text-alt text-error"><?php echo e($message); ?></span>
                                    </label>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        <?php endif; ?>

                        <!-- Livro Selecionado (se vier da página de livros) -->

                        <?php if(isset($livro)): ?>

                            <input type="hidden" name="livro_id" value="<?php echo e($livro->id); ?>">
                            <div class="card bg-base-200 mb-6">
                                <div class="card-body">
                                    <h3 class="card-title text-lg">Livro Selecionado</h3>
                                    <div class="flex gap-4">
                                        <?php if($livro->imagem_capa): ?>
                                            <img src="<?php echo e(asset('storage/' . $livro->imagem_capa)); ?>" alt="<?php echo e($livro->nome); ?>" class="h-32 w-24 object-cover rounded shadow">
                                        <?php endif; ?>
                                        <div class="flex-1">
                                            <p class="font-bold"><?php echo e($livro->nome); ?></p>
                                            <p class="text-sm text-base-content/60">ISBN: <?php echo e($livro->isbn); ?></p>
                                            <p class="text-sm text-base-content/60">Editora: <?php echo e($livro->editora->nome ?? 'N/A'); ?></p>
                                            <p class="text-sm text-base-content/60">
                                                Autores: <?php echo e($livro->autores->pluck('nome')->join(', ') ?: 'Sem autor'); ?>

                                            </p>
                                            <?php if($livro->preco): ?>
                                                <p class="text-sm font-semibold mt-2">Preço: € <?php echo e(number_format($livro->preco, 2, ',', '.')); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>


                        <!-- Foto do Cidadão -->

                        <div class="form-control w-full mb-6">
                            <label class="label">
                                <span class="label-text font-semibold">Foto de Identificação <span class="text-error">*</span></span>
                                <span class="label-text-alt">Obrigatório</span>
                            </label>
                            <input

                                type="file"
                                name="foto_cidadao"
                                accept="image/*"
                                class="file-input file-input-bordered w-full <?php $__errorArgs = ['foto_cidadao'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> file-input-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                required
                            >

                            <?php $__errorArgs = ['foto_cidadao'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <label class="label">
                                    <span class="label-text-alt text-error"><?php echo e($message); ?></span>
                                </label>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            <label class="label">
                                <span class="label-text-alt text-base-content/60">Envie uma foto sua para registo da requisição</span>
                            </label>
                        </div>

                        <!-- Observações -->
                        <div class="form-control w-full mb-6">
                            <label class="label">
                                <span class="label-text font-semibold">Observações</span>
                                <span class="label-text-alt">Opcional</span>
                            </label>
                            <textarea
                                name="observacoes"
                                rows="3"
                                placeholder="Adicione alguma observação sobre esta requisição..."
                                class="textarea textarea-bordered w-full <?php $__errorArgs = ['observacoes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> textarea-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            ><?php echo e(old('observacoes')); ?></textarea>
                            <?php $__errorArgs = ['observacoes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <label class="label">
                                    <span class="label-text-alt text-error"><?php echo e($message); ?></span>
                                </label>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <!-- Botões -->
                        <div class="flex justify-end gap-3 pt-4 border-t border-base-300">
                            <a href="<?php echo e(route('requisicoes.index')); ?>" class="btn btn-ghost">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Criar Requisição
                            </button>
                        </div>
                    </form>
                </div>
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
<?php /**PATH C:\Users\Utilizador\Herd\biblioteca\resources\views/requisicoes/create.blade.php ENDPATH**/ ?>