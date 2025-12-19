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
                Checkout - Morada de Entrega
            </h2>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Breadcrumb -->
            <div class="text-sm breadcrumbs mb-6">
                <ul>
                    <li><a href="<?php echo e(route('carrinho.index')); ?>">Carrinho</a></li>
                    <li class="font-bold">Finalizar Encomenda</li>
                </ul>
            </div>

            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h3 class="card-title text-2xl mb-4">Informações de Entrega</h3>

                    <form method="POST" action="<?php echo e(route('checkout.shipping.process')); ?>">
                        <?php echo csrf_field(); ?>

                        <!-- Nome Completo -->
                        <div class="form-control w-full">
                            <label class="label">
                                <span class="label-text">Nome Completo <span class="text-error">*</span></span>
                            </label>
                            <input type="text" name="nome_completo" value="<?php echo e(old('nome_completo', auth()->user()->name)); ?>" class="input input-bordered w-full <?php $__errorArgs = ['nome_completo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> input-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                            <?php $__errorArgs = ['nome_completo'];
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

                        <!-- Email -->
                        <div class="form-control w-full mt-4">
                            <label class="label">
                                <span class="label-text">Email <span class="text-error">*</span></span>
                            </label>
                            <input type="email" name="email" value="<?php echo e(old('email', auth()->user()->email)); ?>" class="input input-bordered w-full <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> input-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                            <?php $__errorArgs = ['email'];
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

                        <!-- Telefone -->
                        <div class="form-control w-full mt-4">
                            <label class="label">
                                <span class="label-text">Telefone</span>
                            </label>
                            <input type="tel" name="telefone" value="<?php echo e(old('telefone')); ?>" class="input input-bordered w-full <?php $__errorArgs = ['telefone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> input-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                            <?php $__errorArgs = ['telefone'];
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

                        <!-- Morada -->
                        <div class="form-control w-full mt-4">
                            <label class="label">
                                <span class="label-text">Morada Completa <span class="text-error">*</span></span>
                            </label>
                            <textarea name="morada" rows="3" class="textarea textarea-bordered w-full <?php $__errorArgs = ['morada'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> textarea-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required><?php echo e(old('morada')); ?></textarea>
                            <?php $__errorArgs = ['morada'];
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

                        <!-- Cidade e Código Postal -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <!-- Cidade -->
                            <div class="form-control w-full">
                                <label class="label">
                                    <span class="label-text">Cidade <span class="text-error">*</span></span>
                                </label>
                                <input type="text" name="cidade" value="<?php echo e(old('cidade')); ?>" class="input input-bordered w-full <?php $__errorArgs = ['cidade'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> input-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                <?php $__errorArgs = ['cidade'];
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

                            <!-- Código Postal -->
                            <div class="form-control w-full">
                                <label class="label">
                                    <span class="label-text">Código Postal <span class="text-error">*</span></span>
                                </label>
                                <input type="text" name="codigo_postal" value="<?php echo e(old('codigo_postal')); ?>" placeholder="0000-000" class="input input-bordered w-full <?php $__errorArgs = ['codigo_postal'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> input-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                <?php $__errorArgs = ['codigo_postal'];
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
                        </div>

                        <!-- País -->
                        <div class="form-control w-full mt-4">
                            <label class="label">
                                <span class="label-text">País <span class="text-error">*</span></span>
                            </label>
                            <input type="text" name="pais" value="<?php echo e(old('pais', 'Portugal')); ?>" class="input input-bordered w-full <?php $__errorArgs = ['pais'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> input-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                            <?php $__errorArgs = ['pais'];
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

                        <!-- Resumo da Encomenda -->
                        <div class="divider my-6"></div>

                        <h4 class="font-bold text-lg mb-4">Resumo da Encomenda</h4>

                        <div class="bg-base-200 p-4 rounded-lg space-y-2">
                            <div class="flex justify-between">
                                <span>Items no Carrinho:</span>
                                <span class="font-bold"><?php echo e($carrinhoItems->count()); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span>Subtotal:</span>
                                <span class="font-bold">€<?php echo e(number_format($subtotal, 2)); ?></span>
                            </div>
                        </div>

                        <!-- Opções de Pagamento -->
                        <div class="divider my-6"></div>

                        <div class="alert alert-info mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <div>
                                <h4 class="font-bold">Escolha como pretende continuar</h4>
                                <div class="text-xs">Pode pagar agora ou guardar a encomenda para pagar mais tarde.</div>
                            </div>
                        </div>

                        <!-- Botões -->
                        <div class="card-actions justify-between mt-6">
                            <a href="<?php echo e(route('carrinho.index')); ?>" class="btn btn-ghost">
                                Voltar ao Carrinho
                            </a>
                            <div class="flex gap-3">
                                <button type="submit" name="payment_action" value="pay_later" class="btn btn-outline">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Pagar Depois
                                </button>
                                <button type="submit" name="payment_action" value="pay_now" class="btn btn-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                    </svg>
                                    Pagar Agora
                                </button>
                            </div>
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
<?php /**PATH C:\Users\Utilizador\Herd\biblioteca\resources\views/checkout/shipping.blade.php ENDPATH**/ ?>