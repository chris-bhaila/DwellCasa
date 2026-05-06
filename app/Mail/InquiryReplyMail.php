<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InquiryReplyMail extends Mailable
{
    use Queueable, SerializesModels;

    public $inquiry;
    public $replySubject;
    public $replyMessage;

    public function __construct($inquiry, $replySubject, $replyMessage)
    {
        $this->inquiry = $inquiry;
        $this->replySubject = $replySubject;
        $this->replyMessage = $replyMessage;
        $inquiry->loadMissing('location');
    }

    public function build()
    {
        return $this->subject($this->replySubject)
                    ->view('emails.inquiry-reply');
    }
}
