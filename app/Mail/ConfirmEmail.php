<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ConfirmEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    protected $esubject;
    protected $bookingdata;

    public function __construct($data)
    {
        $this->esubject = $data['subject'];
        $this->bookingdata = $data['bookingdata'];

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->subject($this->esubject)->view('email.confirm_email')->with([
                        'bookingdata' => $this->bookingdata
                    ]);
        
    }
}
