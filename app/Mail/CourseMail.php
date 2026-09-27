<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CourseMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public string $invoicePath,
        public string $coursePath,
        public string $name,
        public string $courseName,
        public string $orderId,
        public string $amount,
        public string $purchaseDate,
        public string $courseUrl,
        public string $invoice,
        public string $subject,

    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'view.course',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->invoicePath)
                ->as('Invoice.pdf')
                ->withMime('application/pdf'),
            Attachment::fromPath($this->coursePath)
                ->as('Invoice.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
