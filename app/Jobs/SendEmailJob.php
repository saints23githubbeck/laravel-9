<?php

namespace App\Jobs;

use App\Mail\MultipleEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $toEmailAddresses;
    public $messageSubject;
    public $messageBody;

    /**
     * Create a new job instance.
     *
     * @param array $toEmailAddresses
     * @param string $messageSubject
     * @param string $messageBody
     */
    public function __construct(array $toEmailAddresses, $messageSubject, $messageBody)
    {
        $this->toEmailAddresses = $toEmailAddresses;
        $this->messageSubject = $messageSubject;
        $this->messageBody = $messageBody;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->toEmailAddresses as $email) {
            Mail::to($email)->send(new MultipleEmail($this->messageSubject, $this->messageBody));
        }
    }
}
