<?php if (isset($component)) { $__componentOriginalaa758e6a82983efcbf593f765e026bd9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalaa758e6a82983efcbf593f765e026bd9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => $__env->getContainer()->make(Illuminate\View\Factory::class)->make('mail::message'),'data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mail::message'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
# Nova Requisicao de Livro

Foi criada uma nova requisicao que aguarda a sua aprovacao.

## Detalhes da Requisicao

**Livro:** <?php echo new \Illuminate\Support\EncodedHtmlString($livro->nome); ?>


**ISBN:** <?php echo new \Illuminate\Support\EncodedHtmlString($livro->isbn); ?>


**Autor(es):** <?php echo new \Illuminate\Support\EncodedHtmlString($livro->autores->pluck('nome')->join(', ')); ?>


**Editora:** <?php echo new \Illuminate\Support\EncodedHtmlString($livro->editora->nome ?? 'N/A'); ?>


---

## Dados do Cidadao

**Nome:** <?php echo new \Illuminate\Support\EncodedHtmlString($cidadao->name); ?>


**Email:** <?php echo new \Illuminate\Support\EncodedHtmlString($cidadao->email); ?>


---

## Informacoes da Requisicao

**Data da Requisicao:** <?php echo new \Illuminate\Support\EncodedHtmlString($requisicao->data_requisicao->format('d/m/Y')); ?>


**Data Prevista Devolucao:** <?php echo new \Illuminate\Support\EncodedHtmlString($requisicao->data_prevista_devolucao->format('d/m/Y')); ?>


<?php if($requisicao->observacoes): ?>
**Observacoes:** <?php echo new \Illuminate\Support\EncodedHtmlString($requisicao->observacoes); ?>

<?php endif; ?>

---

<?php if($livro->imagem_capa): ?>
## Capa do Livro

<img src="<?php echo new \Illuminate\Support\EncodedHtmlString(url('storage/' . $livro->imagem_capa)); ?>" alt="Capa do livro <?php echo new \Illuminate\Support\EncodedHtmlString($livro->nome); ?>" style="max-width: 200px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
<?php endif; ?>

<?php if (isset($component)) { $__componentOriginal15a5e11357468b3880ae1300c3be6c4f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal15a5e11357468b3880ae1300c3be6c4f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => $__env->getContainer()->make(Illuminate\View\Factory::class)->make('mail::button'),'data' => ['url' => url('/requisicoes')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mail::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(url('/requisicoes'))]); ?>
Ver Requisicoes
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal15a5e11357468b3880ae1300c3be6c4f)): ?>
<?php $attributes = $__attributesOriginal15a5e11357468b3880ae1300c3be6c4f; ?>
<?php unset($__attributesOriginal15a5e11357468b3880ae1300c3be6c4f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal15a5e11357468b3880ae1300c3be6c4f)): ?>
<?php $component = $__componentOriginal15a5e11357468b3880ae1300c3be6c4f; ?>
<?php unset($__componentOriginal15a5e11357468b3880ae1300c3be6c4f); ?>
<?php endif; ?>

Cumprimentos,<br>
<?php echo new \Illuminate\Support\EncodedHtmlString(config('app.name')); ?>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalaa758e6a82983efcbf593f765e026bd9)): ?>
<?php $attributes = $__attributesOriginalaa758e6a82983efcbf593f765e026bd9; ?>
<?php unset($__attributesOriginalaa758e6a82983efcbf593f765e026bd9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalaa758e6a82983efcbf593f765e026bd9)): ?>
<?php $component = $__componentOriginalaa758e6a82983efcbf593f765e026bd9; ?>
<?php unset($__componentOriginalaa758e6a82983efcbf593f765e026bd9); ?>
<?php endif; ?>
<?php /**PATH C:\Users\Utilizador\Herd\biblioteca\resources\views/emails/requisicoes/nova-admin.blade.php ENDPATH**/ ?>