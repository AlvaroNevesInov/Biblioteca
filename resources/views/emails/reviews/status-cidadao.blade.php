# Review {{ $review->isAtivo() ? 'Aprovado' : 'Recusado' }}



Olá {{ $review->user->name }},



@if($review->isAtivo())

O seu review para o livro **{{ $livro->nome }}** foi **aprovado** e está agora visível publicamente na página do livro.



Obrigado por partilhar a sua opinião!

@else

O seu review para o livro **{{ $livro->nome }}** foi **recusado**.



@if($review->justificacao_recusa)

**Motivo da recusa:**

{{ $review->justificacao_recusa }}

@endif

@endif



**O seu comentário:**

{{ $review->comentario }}



Obrigado,<br>
