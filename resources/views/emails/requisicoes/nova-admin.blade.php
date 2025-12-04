<x-mail::message>
# Nova Requisicao de Livro

Foi criada uma nova requisicao que aguarda a sua aprovacao.

## Detalhes da Requisicao

**Livro:** {{ $livro->nome }}

**ISBN:** {{ $livro->isbn }}

**Autor(es):** {{ $livro->autores->pluck('nome')->join(', ') }}

**Editora:** {{ $livro->editora->nome ?? 'N/A' }}

---

## Dados do Cidadao

**Nome:** {{ $cidadao->name }}

**Email:** {{ $cidadao->email }}

---

## Informacoes da Requisicao

**Data da Requisicao:** {{ $requisicao->data_requisicao->format('d/m/Y') }}

**Data Prevista Devolucao:** {{ $requisicao->data_prevista_devolucao->format('d/m/Y') }}

@if($requisicao->observacoes)
**Observacoes:** {{ $requisicao->observacoes }}
@endif

---

@if($livro->imagem_capa)
## Capa do Livro

<img src="{{ url('storage/' . $livro->imagem_capa) }}" alt="Capa do livro {{ $livro->nome }}" style="max-width: 200px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
@endif

<x-mail::button :url="url('/requisicoes')">
Ver Requisicoes
</x-mail::button>

Cumprimentos,<br>
{{ config('app.name') }}
</x-mail::message>
