<?php

namespace App\Http\Controllers;

use App\Models\Livro;
use App\Models\Editora;
use App\Models\Autor;
use Illuminate\Http\Request;

class LivroController extends Controller
{
    public function index()
    {
        return view('livros.index');
    }

     public function show(Livro $livro)
    {
        // Registrar acesso para tracking de sincronização
        $livro->touch('last_accessed_at');

        $livro->load(['editora', 'autores', 'requisicoes.user']);
        // Separar requisições ativas e passadas

        $requisicoesAtivas = $livro->requisicoes()

            ->with('user')
            ->ativas()
            ->recentes()
            ->get();

        $requisicoesPast = $livro->requisicoes()

            ->with('user')
            ->passadas()
            ->recentes()
            ->get();

        // Obter livros relacionados usando inteligência artificial de texto
        $livrosRelacionados = $livro->livrosRelacionados(5);

        return view('livros.show', compact('livro', 'requisicoesAtivas', 'requisicoesPast', 'livrosRelacionados'));

    }

    public function create()
    {
        $editoras = Editora::orderBy('nome')->get();
        $autores = Autor::orderBy('nome')->get();
        return view('livros.create', compact('editoras', 'autores'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'isbn' => 'required|string|unique:livros,isbn|max:255',
            'nome' => 'required|string|max:255',
            'editora_id' => 'required|exists:editoras,id',
            'bibliografia' => 'nullable|string',
            'imagem_capa' => 'nullable|image|max:2048',
            'preco' => 'nullable|numeric|min:0',
            'autores' => 'required|array|min:1',
            'autores.*' => 'exists:autores,id',
        ]);

        if ($request->hasFile('imagem_capa')) {
            $validated['imagem_capa'] = $request->file('imagem_capa')->store('livros', 'public');
        }

        $livro = Livro::create($validated);
        $livro->autores()->attach($request->autores);

        return redirect()->route('livros.index')
            ->with('success', 'Livro criado com sucesso!');
    }

    public function edit(Livro $livro)
    {
        // Registrar acesso para tracking de sincronização
        $livro->touch('last_accessed_at');

        $editoras = Editora::orderBy('nome')->get();
        $autores = Autor::orderBy('nome')->get();
        return view('livros.edit', compact('livro', 'editoras', 'autores'));
    }

    public function update(Request $request, Livro $livro)
    {
        $validated = $request->validate([
            'isbn' => 'required|string|max:255|unique:livros,isbn,' . $livro->id,
            'nome' => 'required|string|max:255',
            'editora_id' => 'required|exists:editoras,id',
            'bibliografia' => 'nullable|string',
            'imagem_capa' => 'nullable|image|max:2048',
            'preco' => 'nullable|numeric|min:0',
            'autores' => 'required|array|min:1',
            'autores.*' => 'exists:autores,id',
        ]);

        if ($request->hasFile('imagem_capa')) {
            $validated['imagem_capa'] = $request->file('imagem_capa')->store('livros', 'public');
        }

        $livro->update($validated);
        $livro->autores()->sync($request->autores);

        return redirect()->route('livros.index')
            ->with('success', 'Livro atualizado com sucesso!');
    }

    public function destroy(Livro $livro)
    {
        $livro->delete();

        return redirect()->route('livros.index')
            ->with('success', 'Livro excluído com sucesso!');
    }

    public function criarEditora(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255|unique:editoras,nome',
        ]);

        $editora = Editora::create($validated);

        return response()->json([
            'id' => $editora->id,
            'nome' => $editora->nome,
        ]);
    }

    public function criarAutor(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255|unique:autores,nome',
        ]);

        $autor = Autor::create($validated);

        return response()->json([
            'id' => $autor->id,
            'nome' => $autor->nome,
        ]);
    }
}
