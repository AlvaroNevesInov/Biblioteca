<?php

namespace App\Http\Controllers;

use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LogController extends Controller
{
    /**
     * Exibir a página de logs (apenas para administradores)
     */
    public function index(): View
    {
        return view('logs.index');
    }

    /**
     * Obter estatísticas dos logs
     */
    public function stats()
    {
        $totalLogs = Log::count();
        $logsHoje = Log::whereDate('created_at', today())->count();
        $logsSemana = Log::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $logsMes = Log::whereMonth('created_at', now()->month)->count();

        $modulosMaisAtivos = Log::selectRaw('modulo, COUNT(*) as total')
            ->groupBy('modulo')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $acoesMaisComuns = Log::selectRaw('acao, COUNT(*) as total')
            ->groupBy('acao')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return response()->json([
            'total_logs' => $totalLogs,
            'logs_hoje' => $logsHoje,
            'logs_semana' => $logsSemana,
            'logs_mes' => $logsMes,
            'modulos_mais_ativos' => $modulosMaisAtivos,
            'acoes_mais_comuns' => $acoesMaisComuns,
        ]);
    }
}
