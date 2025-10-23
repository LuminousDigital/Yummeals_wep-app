<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RaffleQualification extends Mailable
{
    use Queueable, SerializesModels;

    public string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function build()
    {
        return $this->subject('You now qualify for our raffle draw!')
            ->view('emails.raffleQualification')
            ->with(['name' => $this->name]);
    }
}
