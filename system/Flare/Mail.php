<?php

namespace FPHP;

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
    private static $_newLine = "\r\n";

    /**
     * 
     * @var string
     */
    private $_body = null;

    /**
     * 
     * @var string
     */
    private $_from = null;

    /**
     * 
     * @var array
     */
    private $_to = array();

    /**
     * 
     * @var array
     */
    private $_cc = array();

    /**
     * 
     * @var array
     */
    private $_bcc = array();

    /**
     * 
     * @var string
     */
    private $_subject = null;

    /**
     * 
     * @var string
     */
    private $_type = null;

    /**
     * 
     * @return string
     */
    private $_error = null;

    /**
     * 
     * @var array
     */
    private $_smtp = array();

    /**
     * 
     * @param array $details
     * @return \FPHP\Mail
     */
    public function setSmtp(array $details)
    {
        $smtp = array();
        $smtp['host'] = isset($details['host']) ? $details['host'] : '';
        $smtp['username'] = isset($details['username']) ? $details['username']: '';
        $smtp['password'] = isset($details['password']) ? $details['password'] : '';
        $smtp['port'] = isset($details['port']) ? $details['port'] : '25';
        $smtp['timeout'] = isset($details['timeout']) ? $details['timeout'] : 5;
        $this->_smtp = $smtp;
        return $this;
    }

    /**
     * 
     * @return array
     */
    public function getSmtp()
    {
        return $this->_smtp;
    }

    /**
     * 
     * @param string $content
     * @return \FPHP\Mail
     */
    public function setContent($content)
    {
        $this->_body = (string) $content;
        return $this;
    }

    /**
     * 
     * @param string $path
     * @param array $data
     * @return \FPHP\Mail
     */
    public function setContentPath($path, $data = array())
    {
        $content = @file_get_contents($path);
        if($content === false){
            throw new Exception("Error occured while parsing given path");
        }

        if($this->_type === 'html'){
            $keys = array_keys($data);
            $content = str_replace($keys, $data, $content);
        }

        return $this->setContent($content);
    }

    /**
     * 
     * @param string $file
     * @return \FPHP\Mail
     */
    public function addAttachment($file)
    {
        
        return $this;
    }

    /**
     * 
     * @param string $type
     * @return \FPHP\Mail
     */
    public function setContentType($type, $additional_parameters = '')
    {
        $this->_type = strtolower((string) $type);
        return $this->addHeader("Content-Type: text/".$type."; {$additional_parameters}");
    }

    /**
     * 
     * @return string
     */
    public function getContentType()
    {
        return $this->_type;
    }

    /**
     * 
     * @return string
     */
    public function getContent()
    {
        return $this->_body;
    }

    /**
     * 
     * @param string $from
     * @param string $name
     * @return \FPHP\Mail
     */
    public function setSender($email, $name = null)
    {
        $this->_from = $email;
        if($name){
            $this->_from = $name." <".$email.">";
        }
        return $this;
    }

    /**
     * 
     * @param string $email
     * @param string $name
     * @return \FPHP\Mail
     */
    public function addRecipient($email, $name = null)
    {
        if($name){
            $email = $name." <".$email.">";
        }
        $this->_to[] = $email;
        return $this;
    }

    /**
     * 
     * @param string $email
     * @param string $name
     * @return \FPHP\Mail
     */
    public function addCc($email, $name = null)
    {
        if($name){
            $email = $name." <".$email.">";
        }
        $this->_cc[] = $email;
        return $this;
    }

    /**
     * 
     * @param string $email
     * @param string $name
     * @return \FPHP\Mail
     */
    public function addBcc($email, $name = null)
    {
        if($name){
            $email = $name." <".$email.">";
        }
        $this->_bcc[] = $email;
        return $this;
    }

    /**
     * 
     * @param string $subject
     * @return \FPHP\Mail
     */
    public function setSubject($subject)
    {
        $this->_subject = (string) $subject;
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getSubject()
    {
        return $this->_subject;
    }

    /**
     * 
     * @param string $header
     * @return \FPHP\Mail
     */
    public function addHeader($header)
    {
        $this->_headers[] = $header;
        return $this;
    }

    /**
     * 
     * @return array
     */
    public function getCc()
    {
        return $this->_cc;
    }

    /**
     * 
     * @return array
     */
    public function getBcc()
    {
        return $this->_bcc;
    }

    /**
     * 
     * @return array
     */
    public function getRecipients()
    {
        return $this->_to;
    }

    /**
     * 
     * @return string
     */
    public function getSender()
    {
        return $this->_from;
    }

    /**
     * 
     * @return array 
     */
    private function _compose()
    {
        $header = 'To: '.implode($this->_to, ', ').self::$_newLine;
        $header .= 'Cc: '.implode($this->_cc, ', ').self::$_newLine;
        $header .= 'Bcc: '.implode($this->_bcc, ', ').self::$_newLine;
        return array(
            'header' => $header,
            'message' => $this->_body,
            'from' => $this->_from
        );
    }

    /**
     * 
     * @return boolean
     */
    public function send()
    {
        $mail = $this->_compose();
        if($this->_smtp){
            fsockopen($this->_smtp['host'], $this->_smtp['port'], $errno, $this->_error);


        }
    }

    /**
     * 
     * @return string
     */
    public function getError()
    {
        return $this->_error;
    }

    /**
     * 
     * @return \FPHP\Mail
     */
    public function clear()
    {
        $this->_body = null;
        $this->_from = array();
        $this->_to = array();
        $this->_cc = array();
        $this->_bcc = array();
        $this->_headers = array();
        $this->_subject = null;
        $this->_error = null;
        $this->_type = null;
        return $this;
    }
}