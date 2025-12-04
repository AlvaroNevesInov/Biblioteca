<?php

namespace App\Http\Controllers;

use App\Models\Requisicao;
use App\Models\Livro;
use App\Models\User;
use App\Models\AvailabilityAlert;
use App\Mail\NovaRequisicaoAdmin;
use App\Mail\NovaRequisicaoCidadao;
use App\Mail\LivroDisponivel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class RequisicaoController extends Controller
{
    /**
     * Display a listing of requisições.
     */
    public function index()
    {
        return view('requisicoes.index');
    }

    /**
     * Show the form for creating a new requisição.
     */
    public function create(Request $request)
    {
        $user = optional(Auth::user());

        // Verificar se o cidadão pode requisitar mais livros (máximo 3)
        if (!$user->podeRequisitar()) {
            return redirect()->route('requisicoes.index')
                ->with('error', 'Já atingiu o limite máximo de 3 livros requisitados em simultâneo. Devolva um livro para poder requisitar outro.');
        }

        $livro = null;
        if ($request->has('livro_id')) {
            $livro = Livro::with(['editora', 'autores'])->findOrFail($request->livro_id);

            // Registrar acesso para tracking de sincronização
            $livro->touch('last_accessed_at');

            if (!$livro->estaDisponivel()) {
                return redirect()->route('livros.index')
                    ->with('error', 'Este livro já está requisitado e não está disponível no momento.');
            }
        }

        $livros = Livro::with(['editora', 'autores'])
            ->orderBy('nome')
            ->get()
            ->filter(fn($livro) => $livro->estaDisponivel());

        return view('requisicoes.create', compact('livros', 'livro'));
    }

    /**
     * Store a newly created requisição in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'livro_id' => 'required|exists:livros,id',
            'foto_cidadao' => 'required|image|max:2048',
            'observacoes' => 'nullable|string|max:500',
        ]);

        $user = optional(Auth::user());

        // Verificar se o cidadão pode requisitar mais livros (máximo 3)
        if (!$user->podeRequisitar()) {
            return redirect()->route('requisicoes.index')
                ->with('error', 'Já atingiu o limite máximo de 3 livros requisitados em simultâneo.');
        }

        $livro = Livro::findOrFail($validated['livro_id']);

        // Registrar acesso para tracking de sincronização (livro está sendo requisitado)
        $livro->touch('last_accessed_at');

        if (!$livro->estaDisponivel()) {
            return redirect()->route('requisicoes.create')
                ->with('error', 'Este livro já não está disponível para requisição.');
        }

        $requisicaoExistente = Requisicao::where('user_id', Auth::id())
            ->where('livro_id', $validated['livro_id'])
            ->whereIn('estado', ['pendente', 'aprovada'])
            ->exists();

        if ($requisicaoExistente) {
            return redirect()->route('requisicoes.index')
                ->with('error', 'Já tem uma requisição ativa deste livro.');
        }

        // Upload da foto do cidadão (obrigatório - já validado)
        $file = $request->file('foto_cidadao');
        $filename = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
        $uploadPath = public_path('uploads/requisicoes');

        // Criar pasta se não existir
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $file->move($uploadPath, $filename);
        $fotoCidadao = '/uploads/requisicoes/' . $filename;

        // Se o cidadão ainda não tem foto de perfil, copia a foto para public/uploads/profile-photos
        if (!$user->profile_photo_path) {
            // Criar pasta de profile photos se não existir
            $profilePhotosPath = public_path('uploads/profile-photos');
            if (!file_exists($profilePhotosPath)) {
                mkdir($profilePhotosPath, 0755, true);
            }

            // Copiar a foto para public/uploads/profile-photos
            $sourceFile = $uploadPath . '/' . $filename;
            $destFile = $profilePhotosPath . '/' . $filename;
            copy($sourceFile, $destFile);

            $user->update([
                'profile_photo_path' => '/uploads/profile-photos/' . $filename
            ]);
        }

        $requisicao = Requisicao::create([
            'user_id' => Auth::id(),
            'livro_id' => $validated['livro_id'],
            'foto_cidadao' => $fotoCidadao,
            'estado' => 'pendente',
            'data_requisicao' => Carbon::today(),
            'data_prevista_devolucao' => Carbon::today()->addDays(5),
            'observacoes' => $validated['observacoes'] ?? null,
        ]);

        // Carregar relações para os emails
        $requisicao->load(['livro.autores', 'livro.editora', 'user']);

        // Enviar email para o cidadão (com pequeno atraso)

        Mail::to($user->email)

            ->later(now()->addSeconds(5), new NovaRequisicaoCidadao($requisicao));

        // Enviar email para todos os administradores (com atraso maior)

        $admins = User::where('role', 'admin')->get();

        $delay = 65; // segundos - tempo suficiente após o primeiro email

        foreach ($admins as $admin) {

            Mail::to($admin->email)

                ->later(now()->addSeconds($delay), new NovaRequisicaoAdmin($requisicao));

            $delay += 10; // incrementa para múltiplos admins
        }

        return redirect()->route('requisicoes.index')
            ->with('success', 'Requisição criada com sucesso! Aguarde aprovação do administrador.');
    }

    /**
     * Show the form for editing the specified requisição (apenas admin).
     */
    public function edit(Requisicao $requisicao)
    {
        if (!optional(Auth::user())->isAdmin()) {
            abort(403, 'Acesso negado.');
        }

        $livros = Livro::with(['editora', 'autores'])->orderBy('nome')->get();

        return view('requisicoes.edit', compact('requisicao', 'livros'));
    }

    /**
     * Update the specified requisição in storage (apenas admin).
     */
    public function update(Request $request, Requisicao $requisicao)
    {
        if (!optional(Auth::user())->isAdmin()) {
            abort(403, 'Acesso negado.');
        }

        $validated = $request->validate([
            'estado' => 'required|in:pendente,aprovada,rejeitada,devolvida',
            'data_prevista_devolucao' => 'nullable|date|after_or_equal:data_requisicao',
            'data_devolucao' => 'nullable|date|after_or_equal:data_requisicao',
            'observacoes' => 'nullable|string|max:500',
        ]);

        $requisicao->update($validated);

        return redirect()->route('requisicoes.index')
            ->with('success', 'Requisição atualizada com sucesso!');
    }

    /**
     * Aprovar uma requisição (apenas admin).
     */
    public function aprovar(Requisicao $requisicao)
    {
        if (!optional(Auth::user())->isAdmin()) {
            abort(403, 'Acesso negado.');
        }

        if (!$requisicao->livro->estaDisponivel() && $requisicao->estado !== 'pendente') {
            return redirect()->route('requisicoes.index')
                ->with('error', 'Este livro já não está disponível.');
        }

        $requisicao->update(['estado' => 'aprovada']);

        return redirect()->route('requisicoes.index')
            ->with('success', 'Requisição aprovada com sucesso!');
    }

    /**
     * Rejeitar uma requisição (apenas admin).
     */
    public function rejeitar(Request $request, Requisicao $requisicao)
    {
        if (!optional(Auth::user())->isAdmin()) {
            abort(403, 'Acesso negado.');
        }

        $validated = $request->validate(['observacoes' => 'nullable|string|max:500']);

        $requisicao->update([
            'estado' => 'rejeitada',
            'observacoes' => $validated['observacoes'] ?? $requisicao->observacoes,
        ]);

        return redirect()->route('requisicoes.index')
            ->with('success', 'Requisição rejeitada.');
    }

    /**
     * Marcar como devolvida (apenas admin).
     */
    public function devolver(Requisicao $requisicao)
    {
        if (!optional(Auth::user())->isAdmin()) {
            abort(403, 'Acesso negado.');
        }

        $requisicao->update([
            'estado' => 'devolvida',
            'data_devolucao' => Carbon::today(),
        ]);

        return redirect()->route('requisicoes.index')
            ->with('success', 'Livro marcado como devolvido!');
    }

    /**
     * Confirmar a recepção do livro (apenas admin).
     */
    public function confirmarRecepcao(Request $request, Requisicao $requisicao)
    {
        if (!optional(Auth::user())->isAdmin()) {
            abort(403, 'Acesso negado.');
        }

        if (!$requisicao->isDevolvida()) {
            return redirect()->route('requisicoes.index')
                ->with('error', 'Apenas requisições devolvidas podem ter a recepção confirmada.');
        }

        $validated = $request->validate([
            'data_recepcao' => 'required|date|after_or_equal:' . $requisicao->data_requisicao->format('Y-m-d'),
        ]);

        $requisicao->update([
            'data_recepcao' => $validated['data_recepcao'],
            'recebido_por' => Auth::id(),
        ]);

        $diasDecorridos = $requisicao->diasDecorridos();
        $diasAtraso = $requisicao->diasAtraso();

        $mensagem = "Recepção do livro confirmada! Dias decorridos: {$diasDecorridos}";
        if ($diasAtraso > 0) {
            $mensagem .= " (Atraso de {$diasAtraso} dias)";
        }

        // Notificar cidadãos interessados no livro
        $this->notificarInteressados($requisicao->livro);

        return redirect()->route('requisicoes.index')
            ->with('success', $mensagem);
    }

    /**
     * Remove the specified requisição from storage (apenas criador ou admin).
     */
    public function destroy(Requisicao $requisicao)
    {
        $user = Auth::user();

        if (!optional($user)->isAdmin() && $requisicao->user_id !== Auth::id()) {
            abort(403, 'Acesso negado.');
        }

        if (!$requisicao->isPendente()) {
            return redirect()->route('requisicoes.index')
                ->with('error', 'Apenas requisições pendentes podem ser canceladas.');
        }

        $requisicao->delete();

        return redirect()->route('requisicoes.index')
            ->with('success', 'Requisição cancelada com sucesso!');
    }

    /**
     * Notificar cidadãos interessados quando o livro fica disponível.
     */
    private function notificarInteressados(Livro $livro)
    {
        // Verificar se o livro está realmente disponível
        if (!$livro->estaDisponivel()) {
            return;
        }

        // Buscar alertas não notificados para este livro
        $alertas = AvailabilityAlert::where('livro_id', $livro->id)
            ->where('notificado', false)
            ->with('user')
            ->get();

        if ($alertas->isEmpty()) {
            return;
        }

        // Enviar email para cada cidadão interessado
        $delay = 0;
        foreach ($alertas as $alerta) {
            Mail::to($alerta->user->email)
                ->later(now()->addSeconds($delay), new LivroDisponivel($livro, $alerta->user));

            // Marcar como notificado
            $alerta->marcarComoNotificado();

            // Incrementar delay para evitar rate limit (5 segundos entre cada email)
            $delay += 5;
        }
    }
}
