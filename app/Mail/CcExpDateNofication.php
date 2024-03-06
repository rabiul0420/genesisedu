<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class CcExpDateNofication extends Mailable
{
    use Queueable, SerializesModels;

    protected $tmpore_subject;
    protected $firstName;
    protected $last4;

    public function __construct($data)
    {
        $this->tmpore_subject = $data['subject'];
        $this->firstName = $data['firstName'];
        $this->last4 = $data['last4'];
    }
   

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->tmpore_subject)->view('email.cc_exp_notify')->with([
                        'firstName' => $this->firstName,'last4'=>$this->last4
                    ]);
    }
}
