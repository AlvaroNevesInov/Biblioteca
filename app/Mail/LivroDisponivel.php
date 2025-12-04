<?php

namespace App\Mail;

use App\Models\Livro;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LivroDisponivel extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * O livro que ficou disponível.
     */
    public Livro $livro;

    /**
     * O utilizador a ser notificado.
     */
    public User $user;

    /**
     * Create a new message instance.
     */
    public function __construct(Livro $livro, User $user)
    {
        $this->livro = $livro;
        $this->user = $user;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Livro Disponível - ' . $this->livro->nome,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.availability-alerts.livro-disponivel',
            with: [
                'livro' => $this->livro,
                'user' => $this->user,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
