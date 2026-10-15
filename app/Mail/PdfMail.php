<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PdfMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $path,
        public string $name,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Daily Update - Apex Online',
        );
    }

    public function content(): Content
    {
        return new Content(
            html: '<h2>' . e($this->name) . '</h2>',
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->path)
                ->as('Daily Update.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
