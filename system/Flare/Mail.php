<?php

namespace Flare;

/**
 * 
 * @author anthony
 * 
 */
class Mail
{
    /**
     * 
     * @var string
     */
    private $message;

    /**
     * 
     * @var array
     */
    private $cc;

    /**
     * 
     * @var array
     */
    private $bcc;

    /**
     * 
     * @var string
     */
    private $from;

    /**
     * 
     * @var array
     */
    private $to;

    /**
     * 
     * @param string $from
     * @param string $to
     * @param string $body
     */
    public function __construct($from = null, $to = null, $body = null)
    {
        if ($from) $this->setFrom($from);
        if ($to) $this->addTo($to);
        if ($body) $this->setBody($body);
    }

    /**
     * 
     * @param string $message
     * @return \Flare\Mail
     */
    public function setBody($message)
    {
        $this->message = (string) $message;
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getBody()
    {
        return $this->message;
    }

    /**
     * 
     * @param string $email
     * @param string $name
     * @return \Flare\Mail
     */
    public function addCc($email, $name = null)
    {
        $this->cc[] = $name ? $name.' <'.$email.'>' : $email;
        return $this;
    }

    /**
     * 
     * @return array
     */
    public function getCc()
    {
        return $this->cc;
    }

    /**
     * 
     * @param string $email
     * @param string $name
     * @return \Flare\Mail
     */
    public function addBcc($email, $name = null)
    {
        $this->bcc[] = $name ? $name.' <'.$email.'>' : $email;
        return $this;
    }

    /**
     * 
     * @return array
     */
    public function getBcc()
    {
        return $this->bcc;
    }

    /**
     * 
     * @param string $email
     * @param string $name
     * @return \Flare\Mail
     */
    public function addTo($email, $name = null)
    {
        $this->to[] = $name ? $name.' <'.$email.'>' : $email;
        return $this;
    }

    /**
     * 
     * @param string $email
     * @param string $name
     * @return \Flare\Mail
     */
    public function setFrom($email, $name = null)
    {
        $this->from = $name ? $name.' <'.$email.'>' : $email;
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * 
     * @return boolean
     */
    public function send()
    {
        return false;
    }
}