<?php

namespace Flare\Http;

use Flare\Flare as F;

/**
 *
 * @author anthony
 *
 */
class Response
{
    /**
     *
     * @var array
     */
    public static $messages = array(

        // Informational 1xx
        100 => 'Continue',
        101 => 'Switching Protocols',

        // Success 2xx
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',

        // Redirection 3xx
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',  // 1.1
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',

        // 306 is deprecated but reserved
        307 => 'Temporary Redirect',

        // Client Error 4xx
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',

        // Server Error 5xx
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        509 => 'Bandwidth Limit Exceeded'
    );

    /**
     *
     * @var array
     */
    protected $_headers = array();

    /**
     *
     * @var string
     */
    protected $_body = null;

    /**
     *
     * @var int
     */
    protected $_code = 200;

    /**
     * 
     * @var string
     */
    private $_contentType = null;

    /**
     * 
     * @var boolean
     */
    protected $_sent = false;

    /**
     *
     * @param string $key
     * @param string $value
     * @return \Flare\Http\Response
     */
    public function setHeader($key, $value, $auto_send = false)
    {
        $this->_headers[$key] = $value;
        if ($auto_send) {
            header("{$key}: {$value}");
        }
        return $this;
    }

    /**
     *
     * @param string $type
     * @return \Flare\Http\Response
     */
    public function setContentType($type)
    {
        $this->_contentType = $type;
        return $this->setHeader('Content-Type', $type);
    }

    /**
     * 
     * @return string
     */
    public function getContentType()
    {
        if (!$this->_contentType) {
            foreach (headers_list() as $header) {
                $header = strtolower($header);
                if (strpos($header, 'content-type') === 0) {
                    return trim(str_replace('content-type: ', '', $header));
                }
            }
            return null;
        }
        return $this->_contentType;
    }

    /**
     * 
     * @return boolean
     */
    public function hasContentType()
    {
        return $this->getContentType() ? true : false;
    }

    /**
     *
     * @param string $view
     * @return \Flare\Http\Response
     */
    public function setBody($view)
    {
        $this->_body = (string) $view;
        return $this;
    }

    /**
     *
     * @param string $url
     * @param int $code
     * @return \Flare\Http\Response
     */
    public function setRedirect($url, $code = 302)
    {
        $this->_headers['Location'] = $url;
        return $this->setCode($code);
    }

    /**
     *
     * @param string $url
     * @param int $code
     * @return void
     */
    public function redirect($url, $code = 302)
    {
        if (parse_url($url, PHP_URL_SCHEME) === null) {
            $url = F::$uri->getBaseUrl().ltrim($url, '/');
        }
        $this->setRedirect($url, $code)->send(false);
    }

    /**
     * 
     * @param int $seconds
     * @param string $url
     * @return void
     */
    public function setRefresh($seconds = 0, $url = null)
    {
        if (is_string($seconds)) {
            $seconds = (int) $seconds;
        }
        if (!$url) {
            $url = F::$uri->getCurrentUrl();
        } elseif (parse_url($url, PHP_URL_SCHEME) === null) {
            $url = F::$uri->getBaseUrl().ltrim($url, '/');
        }
        $this->setHeader('Refresh', '{$seconds};url="{$url}"');
    }

    /**
     *
     * @param int $code
     * @return \Flare\Http\Response
     */
    public function setCode($code)
    {
        $this->_code = $code;
        return $this;
    }

    /**
     * 
     * @param string $path
     * @param string $filename
     * @return void
     */
    public function download($path, $filename = null)
    {
        //TODO
    }

    /**
     * 
     * @param string $code
     * @param string $view
     * @return void
     */
    public function error($code, $view = null)
    {
        //TODO
    }

    /**
     *
     * @return int
     */
    public function getCode()
    {
        return $this->_code;
    }

    /**
     * This must be the last method executed in \Flare\Application\Mvc::dispatch()
     * @return void
     */
    public function send($output_body = true)
    {
        if ($this->_sent) {
            show_response(500, "Response::send already executed");
        }
        if ($this->_code !== 200 && isset(self::$messages[$this->_code])) {
            if (!empty($_SERVER['SERVER_PROTOCOL'])) {
                header($_SERVER['SERVER_PROTOCOL'].' '.$this->_code.' '.self::$messages[$this->_code]);
            } else {
                header('HTTP/1.1 '.$this->_code.' '.self::$messages[$this->_code]);
            }
        }
        if (isset($this->_headers['Location']) && (300 <= $this->_code) && (307 >= $this->_code)) {
            header('Location: '.$this->_headers['Location']);
            exit;
        }

        foreach ($this->_headers as $key => $header) {
            header("{$key}: {$header}");
        }

        if ($output_body) {
            echo $this->_body;
        }
        $this->_sent = true;
    }
}

