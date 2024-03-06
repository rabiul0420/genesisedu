<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class LoginAttempt extends Mailable
{
    use Queueable, SerializesModels;

    protected $tmpore_subject;
    protected $tmpore_message;

    public function __construct($data)
    {
        $this->tmpore_subject = $data['subject'];
        $this->username = $data['username'];
        $this->tmpore_message = $data['message'];
    }


    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->tmpore_subject)->view('email.login_attempt')->with([
            'tempore_message' => $this->tmpore_message,'username'=>$this->username
        ]);
    }
}
