<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReviewRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Booking $booking,
        public string $token
    ) {
        $booking->loadMissing('location');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'How was your stay at ' . ($this->booking->location->name ?? 'DwellCasa') . '?',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.review-request',
        );
    }
}