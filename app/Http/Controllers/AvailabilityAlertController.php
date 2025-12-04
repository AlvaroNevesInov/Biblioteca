<?php

namespace App\Http\Controllers;

use App\Models\AvailabilityAlert;
use App\Models\Livro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AvailabilityAlertController extends Controller
{
    /**
     * Registar interesse em ser notificado quando o livro ficar disponível.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'livro_id' => 'required|exists:livros,id',
        ]);

        $user = Auth::user();
        $livro = Livro::findOrFail($validated['livro_id']);

        // Verificar se o livro está disponível
        if ($livro->estaDisponivel()) {
            return redirect()->back()
                ->with('error', 'Este livro já está disponível para requisição. Não precisa de alerta.');
        }

        // Verificar se já existe um alerta para este livro e utilizador
        $alertaExistente = AvailabilityAlert::where('user_id', $user->id)
            ->where('livro_id', $livro->id)
            ->exists();

        if ($alertaExistente) {
            return redirect()->back()
                ->with('info', 'Já está registado para receber notificação quando este livro ficar disponível.');
        }

        // Criar o alerta
        AvailabilityAlert::create([
            'user_id' => $user->id,
            'livro_id' => $livro->id,
            'notificado' => false,
        ]);

        return redirect()->back()
            ->with('success', 'Registado com sucesso! Será notificado por email quando o livro "' . $livro->nome . '" ficar disponível.');
    }

    /**
     * Remover alerta de disponibilidade.
     */
    public function destroy(Request $request)
    {
        $validated = $request->validate([
            'livro_id' => 'required|exists:livros,id',
        ]);

        $user = Auth::user();

        $alerta = AvailabilityAlert::where('user_id', $user->id)
            ->where('livro_id', $validated['livro_id'])
            ->first();

        if (!$alerta) {
            return redirect()->back()
                ->with('error', 'Alerta não encontrado.');
        }

        $livroNome = $alerta->livro->nome;
        $alerta->delete();

        return redirect()->back()
            ->with('success', 'Alerta removido com sucesso. Já não receberá notificações sobre "' . $livroNome . '".');
    }

    /**
     * Listar os alertas do utilizador.
     */
    public function index()
    {
        $user = Auth::user();

        $alertas = AvailabilityAlert::where('user_id', $user->id)
            ->with(['livro.editora', 'livro.autores'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('availability-alerts.index', compact('alertas'));
    }
}
