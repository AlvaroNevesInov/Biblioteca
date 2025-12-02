# Novo Review Submetido



Olá Admin,



Um novo review foi submetido por **{{ $cidadao->name }}** para o livro **{{ $livro->nome }}**.



**Comentário:**

{{ $review->comentario }}



**Dados do Cidadão:**

- Nome: {{ $cidadao->name }}

- Email: {{ $cidadao->email }}



O review encontra-se em estado **suspenso** e aguarda a sua aprovação ou recusa.



<x-mail::button :url="$url">

Ver Review

</x-mail::button>



Obrigado,<br>
