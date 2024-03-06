<?php


namespace App\Http\Helpers;


use App\SmsLog;

class Sms
{

    protected $text;
    protected $sender_id = '8809612440402';
    protected $recipient;
    protected $user = 'genesispg';
    protected $password = '123321@12';
    protected $channel = 'Normal';
    protected $DCS = 0;
    protected $flashsms = 0;

    private $response;

    public static function init(){
        return new Sms();
    }

    /**
     * @param mixed $text
     */
    public function setText($text): Sms
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @param mixed $sender_id
     */
    public function setSenderId($sender_id): Sms
    {
        $this->sender_id = $sender_id;
        return $this;
    }

    /**
     * @param mixed $recipient
     */
    public function setRecipient($recipient): Sms
    {
        if( !preg_match('/^88/', $recipient)) {
            $recipient = '88' . $recipient;
        }

        $this->recipient = $recipient;
        return $this;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user): Sms
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password): Sms
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @param mixed $channel
     */
    public function setChannel($channel): Sms
    {
        $this->channel = $channel;
        return $this;
    }

    /**
     * @param int $DCS
     */
    public function setDCS(int $DCS): void
    {
        $this->DCS = $DCS;
    }

    /**
     * @param int $flashsms
     */
    public function setFlashsms(int $flashsms): void
    {
        $this->flashsms = $flashsms;
    }

    /**
     * @return mixed
     */
    protected function getResponse()
    {
        return $this->response;
    }

    public function send(){
        $ch = curl_init();
        $msg = urlencode( $this->text );

        curl_setopt($ch,
            CURLOPT_URL,
            "http://sms4.digitalsynapsebd.com/api/mt/SendSMS?user={$this->user}&password={$this->password}&senderid={$this->sender_id}&channel={$this->channel}&DCS={$this->DCS}&flashsms={$this->flashsms}&number={$this->recipient}&text=$msg");

        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        $response = curl_exec($ch);
        curl_close($ch);
        $this->response = $response;
        return $response;
    }

    public function save_log( $event, $doctor_id, $mob = null, $admin_id = null, $identifier = null ){
        if( $this->response ) {
            $smsLog = new SmsLog( );
            $smsLog->identifier = $identifier;
            $smsLog->set_response( $this->response, $doctor_id, $mob, $admin_id );
            $smsLog->set_event($event);
            $smsLog->save();
        }
    }

}
