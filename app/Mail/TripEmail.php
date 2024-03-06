<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class TripEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    protected $tmpore_subject;
    protected $event_starttime;
    protected $event_location;
    protected $event_title;
    protected $event_link;

    public function __construct($data)
    {
        $this->tmpore_subject = $data['subject'];
        $this->event_starttime = $data['starttime'];
        $this->event_location = $data['location'];
        $this->event_title = $data['title'];
        $this->event_link = $data['link'];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->tmpore_subject)->view('email.newtrip')->with([
                        'event_starttime' => $this->event_starttime,
                        'event_location' => $this->event_location,
                        'event_title' => $this->event_title,
                        'event_link' => $this->event_link,
                        
                    ]);
    }
}
