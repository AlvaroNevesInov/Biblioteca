<x-mail::message>
# Livro Disponível para Requisição

Olá {{ $user->name }},

Temos boas notícias! O livro que você manifestou interesse já está disponível para requisição na biblioteca.

## Detalhes do Livro

**Livro:** {{ $livro->nome }}

**ISBN:** {{ $livro->isbn }}

**Autor(es):** {{ $livro->autores->pluck('nome')->join(', ') }}

**Editora:** {{ $livro->editora->nome ?? 'N/A' }}

---

@if($livro->imagem_capa)
## Capa do Livro

<img src="{{ url('storage/' . $livro->imagem_capa) }}" alt="Capa do livro {{ $livro->nome }}" style="max-width: 200px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
@endif

<x-mail::button :url="url('/livros/' . $livro->id)">
Ver Detalhes e Requisitar
</x-mail::button>

**Nota:** O livro está disponível neste momento, mas pode ser requisitado por outro cidadão a qualquer momento. Recomendamos que faça a sua requisição o mais breve possível.

Cumprimentos,<br>
{{ config('app.name') }}
</x-mail::message>
