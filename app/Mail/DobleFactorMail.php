<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DobleFactorMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $codigo,
        public string $urlFirmada
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Código de Acceso (Admin) - Prácticas UTT');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.doble_factor');
    }
}