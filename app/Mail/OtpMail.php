<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $code,
        public string $mailLocale = 'en'
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->mailLocale === 'am'
            ? 'የFelagi መግቢያ ኮድ'
            : 'Your Felagi Login Code';

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.otp',
            with: [
                'code'   => $this->code,
                'locale' => $this->mailLocale,
            ],
        );
    }
}
