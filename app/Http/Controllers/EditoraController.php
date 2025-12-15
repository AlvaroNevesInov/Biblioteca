<?php

namespace App\Http\Controllers;

use App\Models\Editora;
use App\Services\LogService;
use Illuminate\Http\Request;

class EditoraController extends Controller
{
    public function index()
    {
        return view('editoras.index');
    }

    public function create()
    {
        return view('editoras.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'logotipo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('logotipo')) {
            $validated['logotipo'] = $request->file('logotipo')->store('editoras', 'public');
        }

        $editora = Editora::create($validated);

        LogService::logCreate(
            'Editoras',
            $editora->id,
            "Editora '{$editora->nome}' criada"
        );

        return redirect()->route('editoras.index')
            ->with('success', 'Editora criada com sucesso!');
    }

    public function edit(Editora $editora)
    {
        return view('editoras.edit', compact('editora'));
    }

    public function update(Request $request, Editora $editora)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'logotipo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('logotipo')) {
            $validated['logotipo'] = $request->file('logotipo')->store('editoras', 'public');
        }

        $oldNome = $editora->nome;
        $editora->update($validated);

        LogService::logUpdate(
            'Editoras',
            $editora->id,
            "Editora '{$oldNome}' atualizada" .
            ($oldNome !== $editora->nome ? " (nome alterado para '{$editora->nome}')" : '')
        );

        return redirect()->route('editoras.index')
            ->with('success', 'Editora atualizada com sucesso!');
    }

    public function destroy(Editora $editora)
    {
        $editoraNome = $editora->nome;
        $editoraId = $editora->id;

        $editora->delete();

        LogService::logDelete(
            'Editoras',
            $editoraId,
            "Editora '{$editoraNome}' eliminada permanentemente"
        );

        return redirect()->route('editoras.index')
            ->with('success', 'Editora excluída com sucesso!');
    }
}
