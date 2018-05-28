<?php

namespace App\Classes\Client\Mail;

use Illuminate\Mail\Transport\Transport;
use GuzzleHttp\ClientInterface;
use Swift_Mime_Message;

class BRITransport extends Transport
{
    /**
     * Guzzle client instance.
     *
     * @var \GuzzleHttp\ClientInterface
     */
    protected $client;

    /**
     * The BRI API key.
     *
     * @var string
     */
    protected $key;

    /**
     * The BRI domain.
     *
     * @var string
     */
    protected $domain;

    /**
     * THe BRI API end-point.
     *
     * @var string
     */
    protected $url;

    /**
     * Create a new BRI transport instance.
     *
     * @param  \GuzzleHttp\ClientInterface  $client
     * @param  string  $key
     * @param  string  $domain
     * @return void
     */
    public function __construct(ClientInterface $client, $key, $domain)
    {
        $this->key = $key;
        $this->client = $client;
        $this->setDomain( $domain );
    }

    /**
     * {@inheritdoc}
     */
    public function send(Swift_Mime_Message $message, &$failedRecipients = null)
    {
        if ( ENV("APP_ENV") != "local" ) {
            $data = $this->beforeSendPerformed( $message );
            $res = $this->client->post(
                $this->url . "/send_emailv2"
                , [
                    "form_params" => [
                        "headers" => [
                            "Content-type" => "application/x-www-form-urlencoded"
                        ]
                        , "app_id"  => "mybriapi"
                        , "subject" => $message->getSubject()
                        , "content" => $message->getBody()
                        , "to" => array_keys( $message->getTo() )[0]
                    ],
                ]
            );
            \Log::info("HOST_MAIL : ".$this->url."/send_emailv2");
            $this->getMessageLog( $res );
            $sent = $this->sendPerformed( $message );
        }
        return $this->numberOfRecipients( $message );
    }

    /**
     * Get the HTTP payload for sending the Mailgun message.
     *
     * @param  \Swift_Mime_Message  $message
     * @param  string  $to
     * @return array
     */
    protected function payload(Swift_Mime_Message $message)
    {
        return [
            "app_id"  => "mybriapi",
            "subject" => $message->getSubject(),
            "content" => $message->getBody(),
            "to" => array_keys( $message->getTo() )[0],
        ];
    }

    /**
     * Get all the addresses this message should be sent to.
     *
     * Note that Mandrill still respects CC, BCC headers in raw message itself.
     *
     * @param  \Swift_Mime_Message $message
     * @return array
     */
    protected function getTo(Swift_Mime_Message $message)
    {
        $to = [];

        if ( $message->getTo() ) {
            $to = array_merge( $to, array_keys( $message->getTo() ) );
        }

        if ( $message->getCc() ) {
            $to = array_merge( $to, array_keys( $message->getCc() ) );
        }

        if ( $message->getBcc() ) {
            $to = array_merge( $to, array_keys( $message->getBcc() ) );
        }

        return $to;
    }

    /**
     * Get the API key being used by the transport.
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set the API key being used by the transport.
     *
     * @param  string  $key
     * @return string
     */
    public function setKey($key)
    {
        return $this->key = $key;
    }

    /**
     * Get the domain being used by the transport.
     *
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Set the domain being used by the transport.
     *
     * @param  string  $domain
     * @return void
     */
    public function setDomain($domain)
    {
        $this->url = $domain;

        return $this->domain = $domain;
    }

    /**
     * Get Response Log
     *
     * @return void
     **/
    public function getMessageLog( $res )
    {
        \Log::info( "=== Response code Email Service ===" );
        \Log::info( $res->getStatusCode() );
        \Log::info( "=== Response content Email Service ===" );
        \Log::info( $res->getBody()->getContents() );
        \Log::info( "=== End Email Service ===" );
    }
}