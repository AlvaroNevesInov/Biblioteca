<?php

namespace App\Console\Commands;

use App\Mail\CarrinhoAbandonado;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class EnviarEmailsCarrinhoAbandonado extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'carrinho:enviar-emails-abandonados';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envia emails para utilizadores com carrinhos abandonados há mais de 1 hora';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('A procurar carrinhos abandonados...');

        // Buscar utilizadores com itens no carrinho há mais de 1 hora que ainda não receberam email
        $users = User::whereHas('carrinhoItems', function ($query) {
            $query->where('created_at', '<=', now()->subHour())
                  ->whereNull('abandoned_cart_email_sent_at');
        })->get();

        if ($users->isEmpty()) {
            $this->info('Nenhum carrinho abandonado encontrado.');
            return 0;
        }

        $emailsEnviados = 0;

        foreach ($users as $user) {
            // Verificar se o utilizador tem itens válidos no carrinho
            $carrinhoItems = $user->carrinhoItems()
                ->where('created_at', '<=', now()->subHour())
                ->whereNull('abandoned_cart_email_sent_at')
                ->get();

            if ($carrinhoItems->isNotEmpty()) {
                try {
                    // Enviar email
                    Mail::to($user->email)->send(new CarrinhoAbandonado($user));

                    // Marcar todos os itens como notificados
                    $carrinhoItems->each(function ($item) {
                        $item->update(['abandoned_cart_email_sent_at' => now()]);
                    });

                    $emailsEnviados++;
                    $this->info("Email enviado para: {$user->email}");
                } catch (\Exception $e) {
                    $this->error("Erro ao enviar email para {$user->email}: {$e->getMessage()}");
                }
            }
        }

        $this->info("Total de emails enviados: {$emailsEnviados}");
        return 0;
    }
}
