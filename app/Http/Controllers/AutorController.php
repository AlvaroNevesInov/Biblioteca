<?php

namespace App\Http\Controllers;

use App\Models\Autor;
use App\Services\LogService;
use Illuminate\Http\Request;

class AutorController extends Controller
{
    public function index()
    {
        return view('autores.index');
    }

    public function create()
    {
        return view('autores.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'foto' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('autores', 'public');
        }

        $autor = Autor::create($validated);

        LogService::logCreate(
            'Autores',
            $autor->id,
            "Autor '{$autor->nome}' criado"
        );

        return redirect()->route('autores.index')
            ->with('success', 'Autor criado com sucesso!');
    }

    public function edit(Autor $autor)
    {
        return view('autores.edit', compact('autor'));
    }

    public function update(Request $request, Autor $autor)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'foto' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('autores', 'public');
        }

        $oldNome = $autor->nome;
        $autor->update($validated);

        LogService::logUpdate(
            'Autores',
            $autor->id,
            "Autor '{$oldNome}' atualizado" .
            ($oldNome !== $autor->nome ? " (nome alterado para '{$autor->nome}')" : '')
        );

        return redirect()->route('autores.index')
            ->with('success', 'Autor atualizado com sucesso!');
    }

    public function destroy(Autor $autor)
    {
        $autorNome = $autor->nome;
        $autorId = $autor->id;

        $autor->delete();

        LogService::logDelete(
            'Autores',
            $autorId,
            "Autor '{$autorNome}' eliminado permanentemente"
        );

        return redirect()->route('autores.index')
            ->with('success', 'Autor excluído com sucesso!');
    }
}
