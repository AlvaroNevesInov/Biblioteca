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
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <?php echo e(__('Dashboard')); ?>

            </h2>

            <?php if(auth()->user()->isAdmin()): ?>

                <span class="badge badge-error gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-4 h-4 stroke-current">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    Administrador
                </span>
            <?php else: ?>
                <span class="badge badge-info gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-4 h-4 stroke-current">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Cidadão
                </span>
            <?php endif; ?>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <?php if(auth()->user()->isAdmin()): ?>
                
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

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Card Gestão de Utilizadores -->
                    <a href="<?php echo e(route('admin.users.index')); ?>" class="block">
                        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg hover:shadow-2xl transition-shadow duration-300 transform hover:scale-105">
                            <div class="p-8 text-center">
                                <div class="mb-4">
                                    <svg class="w-20 h-20 mx-auto text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </div>
                                <h3 class="text-2xl font-bold text-gray-800 mb-2">Utilizadores</h3>
                                <p class="text-gray-600">Gerir utilizadores do sistema</p>
                            </div>
                        </div>
                    </a>

                    <!-- Card Requisições -->
                    <a href="<?php echo e(route('requisicoes.index')); ?>" class="block">
                        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg hover:shadow-2xl transition-shadow duration-300 transform hover:scale-105">
                            <div class="p-8 text-center">
                                <div class="mb-4">
                                    <svg class="w-20 h-20 mx-auto text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-3-3v6m9 3H3a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h4l2-3h5a2 2 0 012 2v10a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <h3 class="text-2xl font-bold text-gray-800 mb-2">Requisições</h3>
                                <p class="text-gray-600">Gerir todas as requisições de livros</p>
                            </div>
                        </div>
                    </a>

                    <!-- Card Autores -->
                    <a href="<?php echo e(route('autores.index')); ?>" class="block">
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
                    <a href="<?php echo e(route('livros.index')); ?>" class="block">
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
                    <a href="<?php echo e(route('editoras.index')); ?>" class="block">
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

                    <!-- Card Cidadãos -->
                    <a href="<?php echo e(route('cidadaos.index')); ?>" class="block">
                        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg hover:shadow-2xl transition-shadow duration-300 transform hover:scale-105">
                            <div class="p-8 text-center">
                                <div class="mb-4">
                                    <svg class="w-20 h-20 mx-auto text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <h3 class="text-2xl font-bold text-gray-800 mb-2">Cidadãos</h3>
                                <p class="text-gray-600">Ver histórico de requisições dos cidadãos</p>
                            </div>
                        </div>
                    </a>
                </div>

            <?php else: ?>
                
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
                    <a href="<?php echo e(route('livros.index')); ?>" class="block">
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

                    <!-- Card Minhas Requisições -->
                    <a href="<?php echo e(route('requisicoes.index')); ?>" class="block">
                        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg hover:shadow-2xl transition-shadow duration-300 transform hover:scale-105">
                            <div class="p-8 text-center">
                                <div class="mb-4">
                                    <svg class="w-20 h-20 mx-auto text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-3-3v6m9 3H3a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h4l2-3h5a2 2 0 012 2v10a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <h3 class="text-2xl font-bold text-gray-800 mb-2">Minhas Requisições</h3>
                                <p class="text-gray-600">Consultar e gerir as suas requisições</p>
                            </div>
                        </div>
                    </a>


                    <!-- Card Ver Autores -->
                    <a href="<?php echo e(route('autores.index')); ?>" class="block">
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
                    <a href="<?php echo e(route('editoras.index')); ?>" class="block">
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
            <?php endif; ?>
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
<?php /**PATH C:\Users\Utilizador\Herd\biblioteca\resources\views/dashboard.blade.php ENDPATH**/ ?>