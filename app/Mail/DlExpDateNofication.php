<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class DlExpDateNofication extends Mailable
{
    use Queueable, SerializesModels;

    protected $tmpore_subject;
    protected $firstName;
    protected $licenseID;

    public function __construct($data)
    {
        $this->tmpore_subject = $data['subject'];
        $this->firstName = $data['firstName'];
        $this->licenseID = $data['licenseID'];
    }
   

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->tmpore_subject)->view('email.dl_exp_notify')->with([
                        'firstName' => $this->firstName,'licenseID'=>$this->licenseID
                    ]);
    }
}
