<?php

namespace App\Mail;

use App\Models\BreachIncident;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BreachNoticeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public BreachIncident $incident,
        public string $recipientName = 'User',
        public string $mailLocale = 'am'
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->mailLocale === 'am'
            ? '⚠️ አስፈላጊ የግላዊነት ማሳወቂያ — Felagi'
            : '⚠️ Important Privacy Notice — Felagi';

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.breach-notice',
            with: [
                'incident'      => $this->incident,
                'recipientName' => $this->recipientName,
                'locale'        => $this->mailLocale,
            ],
        );
    }
}
