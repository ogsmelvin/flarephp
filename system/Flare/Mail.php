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
    const MAIL_PROTOCOL = 'mail';

    /**
     * 
     * @var string
     */
    const SMTP_PROTOCOL = 'smtp';

    /**
     * 
     * @var string
     */
    const SENDMAIL_PROTOCOL = 'sendmail';

    /**
     * 
     * @var string
     */
    private $message;

    /**
     * 
     * @var array
     */
    private $headers;

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
     * @var string
     */
    private $protocol = self::MAIL_PROTOCOL;

    /**
     * 
     * @var array
     */
    private $to;

    /**
     * 
     * @var boolean
     */
    private $valid = true;

    /**
     * 
     * @var string
     */
    private $error;

    /**
     * 
     * @var string
     */
    private $subject;

    /**
     * 
     * @var array
     */
    private $smtp = array();

    /**
     * 
     * @param string $from
     * @param string $to
     * @param string $body
     * @param array $headers
     */
    public function __construct($from = null, $to = null, $body = null, $headers = array())
    {
        if ($from) $this->setFrom($from);
        if ($to) $this->addTo($to);
        if ($body) $this->setBody($body);
        if ($headers) $this->setHeaders($headers);
    }

    /**
     * 
     * @param string $header
     * @param string $value
     * @return \Flare\Mail
     */
    public function addHeader($header, $value)
    {
        $this->headers[$header] = $value;
        return $this;
    }

    /**
     * 
     * @param array $headers
     * @return \Flare\Mail
     */
    public function setHeaders(array $headers)
    {
        $this->headers = array();
        foreach ($headers as $header) {
            if (isset($header['name'], $header['value'])) {
                $this->addHeader($header['name'], $header['value']);
            }
        }
        return $this;
    }

    /**
     * 
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
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
     * @param string $subject
     * @return \Flare\Mail
     */
    public function setSubject($subject)
    {
        $this->subject = (string) $subject;
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * 
     * @param string $email
     * @param string $name
     * @return \Flare\Mail
     */
    public function addCc($email, $name = null)
    {
        $this->validateEmail($email);
        $this->cc[] = $name ? $name.' <'.$email.'>' : $email;
        return $this;
    }

    /**
     * 
     * @param string|array $emails
     * @param string $name
     * @return \Flare\Mail
     */
    public function setCc($emails, $name = null)
    {
        $this->cc = array();
        if (is_array($emails)) {
            foreach ($emails as $email) {
                if (is_array($email)) {
                    $this->addCc($email['email'], $email['name']);
                } else {
                    $this->addCc($email);
                }
            }
        } else {
            $this->addCc($emails, $name);
        }
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
        $this->validateEmail($email);
        $this->bcc[] = $name ? $name.' <'.$email.'>' : $email;
        return $this;
    }

    /**
     * 
     * @param string|array $emails
     * @param string $name
     * @return \Flare\Mail
     */
    public function setBcc($emails, $name = null)
    {
        $this->bcc = array();
        if (is_array($emails)) {
            foreach ($emails as $email) {
                if (is_array($email)) {
                    $this->addBcc($email['email'], $email['name']);
                } else {
                    $this->addBcc($email);
                }
            }
        } else {
            $this->addBcc($emails, $name);
        }
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
        $this->validateEmail($email);
        $this->to[] = $name ? $name.' <'.$email.'>' : $email;
        return $this;
    }

    /**
     * 
     * @param string|array $emails
     * @param string $name
     * @return \Flare\Mail
     */
    public function setTo($emails, $name = null)
    {
        $this->to = array();
        if (is_array($emails)) {
            foreach ($emails as $email) {
                if (is_array($email)) {
                    $this->addTo($email['email'], $email['name']);
                } else {
                    $this->addTo($email);
                }
            }
        } else {
            $this->addTo($emails, $name);
        }
        return $this;
    }

    /**
     * 
     * @return array
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * 
     * @param string $email
     * @param string $name
     * @return \Flare\Mail
     */
    public function setFrom($email, $name = null)
    {
        $this->validateEmail($email);
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
     * @param string $hostname
     * @param string $port
     * @param string $username
     * @param string $password
     * @return \Flare\Mail
     */
    public function setSmtp($hostname, $port, $username = null, $password = null)
    {
        $this->smtp['port'] = $port;
        $this->smtp['hostname'] = $hostname;
        $this->smtp['username'] = $username;
        $this->smtp['password'] = $password;
        $this->protocol = self::SMTP_PROTOCOL;
        return $this;
    }

    /**
     * 
     * @return boolean
     */
    public function send()
    {
        $success = false;
        if ($this->validate()) {
            switch ($this->protocol) {
                case self::SMTP_PROTOCOL:
                    $success = $this->sendWithSmtp();
                    break;
                case self::SENDMAIL_PROTOCOL:
                    $success = $this->sendWithSendMail();
                    break;
                default:
                    $success = $this->sendWithMail();
                    break;
            }
        }
        return $success;
    }

    /**
     * 
     * @return boolean
     */
    private function sendWithSmtp()
    {
        return true;
    }

    /**
     * 
     * @return boolean
     */
    private function sendWithMail()
    {
        return true;
    }

    /**
     * 
     * @return boolean
     */
    private function sendWithSendMail()
    {
        return true;
    }

    /**
     * 
     * @return \Flare\Mail
     */
    public function clear()
    {
        $this->cc = array();
        $this->bcc = array();
        $this->from = null;
        $this->to = array();
        $this->message = null;
        $this->headers = array();
        $this->valid = true;
        $this->subject = null;
        $this->protocol = self::MAIL_PROTOCOL;
        $this->smtp = array();
        return $this;
    }

    /**
     * 
     * @return boolean
     */
    private function validate()
    {
        if (!$this->valid) return false;
    }

    /**
     * 
     * @return string
     */
    public function error()
    {
        return $this->error;
    }

    /**
     * 
     * @param string $email
     * @return boolean
     */
    private function validateEmail($email)
    {
        if (!($valid = filter_var($email, FILTER_VALIDATE_EMAIL))) {
            $this->error = "Invalid email address '{$email}'";
            $this->valid = false;
        }
        return $valid;
    }
}