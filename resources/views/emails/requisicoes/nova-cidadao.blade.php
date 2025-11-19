<x-mail::message>
# Confirmacao de Requisicao

A sua requisicao foi registada com sucesso e aguarda aprovacao do administrador.

## Detalhes do Livro Requisitado

**Livro:** {{ $livro->nome }}

**ISBN:** {{ $livro->isbn }}

**Autor(es):** {{ $livro->autores->pluck('nome')->join(', ') }}

**Editora:** {{ $livro->editora->nome ?? 'N/A' }}

---

## Informacoes da Requisicao

**Data da Requisicao:** {{ $requisicao->data_requisicao->format('d/m/Y') }}

**Data Prevista Devolucao:** {{ $requisicao->data_prevista_devolucao->format('d/m/Y') }}

**Estado:** Pendente de Aprovacao

@if($requisicao->observacoes)
**Observacoes:** {{ $requisicao->observacoes }}
@endif

---

@if($livro->imagem_capa)
## Capa do Livro

<img src="{{ asset($livro->imagem_capa) }}" alt="Capa do livro {{ $livro->nome }}" style="max-width: 200px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
@endif

<x-mail::button :url="route('requisicoes.index')">
Ver as Minhas Requisicoes
</x-mail::button>

Sera notificado quando a sua requisicao for processada.

Cumprimentos,<br>
{{ config('app.name') }}
</x-mail::message>
