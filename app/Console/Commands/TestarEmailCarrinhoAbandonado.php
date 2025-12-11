<?php

namespace App\Console\Commands;

use App\Mail\CarrinhoAbandonado;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestarEmailCarrinhoAbandonado extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'carrinho:testar-email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'TESTE: Envia email de carrinho abandonado sem verificar tempo (para testes)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->warn('MODO DE TESTE: Enviando emails sem verificar tempo de abandono');
        $this->info('A procurar carrinhos com itens...');

        // Buscar utilizadores com itens no carrinho (SEM restrição de tempo)
        $users = User::whereHas('carrinhoItems')->get();

        if ($users->isEmpty()) {
            $this->info('Nenhum carrinho encontrado.');
            return 0;
        }

        $emailsEnviados = 0;

        foreach ($users as $user) {
            $carrinhoItems = $user->carrinhoItems;

            if ($carrinhoItems->isNotEmpty()) {
                try {
                    $this->info("Encontrado carrinho de: {$user->name} ({$user->email})");
                    $this->info("  - {$carrinhoItems->count()} item(s) no carrinho");

                    // Enviar email
                    Mail::to($user->email)->send(new CarrinhoAbandonado($user));

                    $emailsEnviados++;
                    $this->info("✓ Email enviado com sucesso!");

                } catch (\Exception $e) {
                    $this->error("✗ Erro ao enviar email: {$e->getMessage()}");
                }
            }
        }

        $this->newLine();
        $this->info("Total de emails enviados: {$emailsEnviados}");
        return 0;
    }
}
