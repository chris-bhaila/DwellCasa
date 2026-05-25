<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $verifyUrl,
        public array  $bookingData,
        public string $locationName,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirm Your Booking Request - ' . $this->bookingData['booking_ref'],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.booking-verification',
        );
    }
}
