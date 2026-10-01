<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerifyEmailMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $token,
        public string $mailLocale = 'en'
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->mailLocale === 'am'
            ? 'የFelagi ኢሜል ማረጋገጫ'
            : 'Verify your Felagi email';

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.verify-email',
            with: [
                'user'      => $this->user,
                'verifyUrl' => url('/verify-email/' . $this->token),
                'locale'    => $this->mailLocale,
            ],
        );
    }
}
