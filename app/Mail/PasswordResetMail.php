<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable
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
            ? 'የFelagi የይለፍ ቃል መቀየሪያ'
            : 'Reset your Felagi password';

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.password-reset',
            with: [
                'user'     => $this->user,
                'resetUrl' => url('/reset-password/' . $this->token . '?email=' . urlencode($this->user->email)),
                'locale'   => $this->mailLocale,
            ],
        );
    }
}
