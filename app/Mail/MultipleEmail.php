<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MultipleEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $messageSubject;
    public $messageBody;

    public function __construct(string $messageSubject, string $messageBody)
    {
        $this->messageSubject = $messageSubject;
        $this->messageBody = $messageBody;
    }

    public function build()
    {
        return $this->from('sender@example.com', 'Arthur Shadrack') // Specify the sender's email address and name here
        ->subject($this->messageSubject) // Set the email subject
        ->view('emailTemplates.bulkEmailTemplate')->with([
            'subject' => $this->messageSubject,
            'body' => $this->messageBody
        ]);
    }
}
