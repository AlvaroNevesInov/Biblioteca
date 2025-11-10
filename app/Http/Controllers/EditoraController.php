<?php

namespace App\Http\Controllers;

use App\Models\Editora;
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

        Editora::create($validated);

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

        $editora->update($validated);

        return redirect()->route('editoras.index')
            ->with('success', 'Editora atualizada com sucesso!');
    }

    public function destroy(Editora $editora)
    {
        $editora->delete();

        return redirect()->route('editoras.index')
            ->with('success', 'Editora excluída com sucesso!');
    }
}
