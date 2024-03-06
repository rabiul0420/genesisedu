<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class TemporeEmail extends Mailable
{
    use Queueable, SerializesModels;

    protected $tmpore_subject;
    protected $tmpore_message;

    public function __construct($data)
    {
        $this->tmpore_subject = $data['subject'];
        $this->tmpore_message = $data['message'];
    }
   

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->tmpore_subject)->view('email.verifycode')->with([
                        'tempore_message' => $this->tmpore_message
                    ]);
    }
}
