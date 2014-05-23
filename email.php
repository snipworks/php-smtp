<?php

/**
 * Send email class using SMTP Authentication
 * @class Email
 */
class Email
{
    const CRLF = "\r\n";
    const TLS = 'tls';
    const SSL = 'ssl';

    protected $server;
    protected $port;
    protected $localhost;
    protected $socket;
    protected $charset;
    protected $username;
    protected $password;
    protected $connect_timeout;
    protected $response_timeout;
    protected $headers;
    protected $content_type;
    protected $from;
    protected $to;
    protected $cc;
    protected $reply_to;
    protected $bcc;
    protected $subject;
    protected $message;
    protected $log;
    protected $is_html;
    protected $protocol;
    

    /**
     * Class constructor
     *  -- Set server name, port and timeout values
     * @param $server
     * @param int $port
     * @param int $connection_timeout
     * @param int $response_timeout
     */
    public function __construct($server, $port = 25, $connection_timeout = 30, $response_timeout = 8)
    {
        $this->server = $server;
        $this->port = $port;
        $this->localhost = $server;
        $this->connect_timeout = $connection_timeout;
        $this->response_timeout = $response_timeout;
        $this->from = array();
        $this->to = array();
        $this->cc = array();
        $this->bcc = array();
        $this->log = array();
        $this->reply_to = array();
        $this->is_html = false;
        $this->protocol = '';
        $this->charset = 'utf-8';
        $this->headers['MIME-Version'] = '1.0';
        $this->headers['Content-type'] = 'text/plain; charset=' . $this->charset;
    }

    /**
     * Add to recipient email address
     * @param $address
     * @param string $name
     */
    public function addTo($address, $name = '')
    {
        $this->to[] = array($address, $name);
    }

    /**
     * Add carbon copy email address
     * @param $address
     * @param string $name
     */
    public function addCc($address, $name = '')
    {
        $this->cc[] = array($address, $name);
    }

    /**
     * Add blind carbon copy email address
     * @param $address
     * @param string $name
     */
    public function addBcc($address, $name = '')
    {
        $this->bcc[] = array($address, $name);
    }

    /**
     * Add email reply to address
     * @param $address
     * @param string $name
     */
    public function addReplyTo($address, $name = '')
    {
        $this->reply_to[] = array($address, $name);
    }

    /**
     * Set SMTP Login authentication
     * @param $username
     * @param $password
     */
    public function setLogin($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * Get message character set
     * @param $charset
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;
    }

    /**
     * Set STMP Server protocol
     * -- default value is null (no secure protocol)
     * @param string $protocol
     */
    public function setProtocol($protocol = '')
    {
        $this->protocol = $protocol;
    }

    /**
     * Set from email address and/or name
     * @param $address
     * @param string $name
     */
    public function setFrom($address, $name = '')
    {
        $this->from = array($address, $name);
    }

    /**
     * Set email subject string
     * @param $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * Set main email body message
     * @param $message
     * @param bool $html
     */
    public function setMessage($message, $html = false)
    {
        $this->message = $message;
        if ($html) {
            $this->headers['Content-type'] = 'text/html; charset=' . $this->charset;
        }
    }

    /**
     * Get log array
     * -- contains commands and responses from SMTP server
     * @return array
     */
    public function getLog()
    {
        return $this->log;
    }

    /**
     * Send email to recipient via mail server
     * @return bool
     */
    public function send()
    {
        $this->socket = fsockopen($this->getServer(), $this->port, $error_number, $error_string, $this->connect_timeout);
        if (empty($this->socket)) {
            return false;
        }

        $this->log['CONNECTION'] = $this->getResponse();
        $this->log['HELLO'] = $this->sendCMD('EHLO ' . $this->localhost);
        $this->log['AUTH'] = $this->sendCMD('AUTH LOGIN');
        $this->log['USERNAME'] = $this->sendCMD(base64_encode($this->username));
        $this->log['PASSWORD'] = $this->sendCMD(base64_encode($this->password));
        $this->log['MAIL_FROM'] = $this->sendCMD('MAIL FROM: <' . $this->from[0] . '>');

        foreach (array_merge($this->to, $this->cc) as $address) {
            $this->log['RECIPIENTS'][] = $this->sendCMD('RCPT TO: <' . $address[0] . '>');
        }

        $this->log['DATA'][1] = $this->sendCMD('DATA');
        if (!empty($this->content_type)) {
            $this->headers['Content-type'] = $this->content_type;
        }

        $this->headers['From'] = $this->formatAddress($this->from);
        $this->headers['To'] = $this->formatAddressList($this->to);
        if (!empty($this->cc)) {
            $this->headers['Cc'] = $this->formatAddressList($this->cc);
        }

        if (!empty($this->bcc)) {
            $this->headers['Bcc'] = $this->formatAddressList($this->bcc);
        }

        if (!empty($this->reply_to)) {
            $this->headers['ReplyTo'] = $this->formatAddressList($this->reply_to);
        }

        $this->headers['Subject'] = $this->subject;
        $this->headers['Date'] = date('r');
        $headers = '';
        foreach ($this->headers as $key => $val) {
            $headers .= $key . ': ' . $val . self::CRLF;
        }

        $this->log['DATA'][2] = $this->sendCMD($headers . self::CRLF . $this->message . self::CRLF . '.');
        $this->log['QUIT'] = $this->sendCMD('QUIT');
        fclose($this->socket);
        return substr($this->log['DATA'][2], 0, 3) == '250';
    }

    /**
     * Get server url
     * -- if set SMTP protocol then prepend it to server
     * @return string
     */
    protected function getServer()
    {
        return ($this->protocol) ? $this->protocol . '://' . $this->server : $this->server;
    }

    /**
     * Get Mail Server response
     * @return string
     */
    protected function getResponse()
    {
        stream_set_timeout($this->socket, $this->response_timeout);
        $response = '';
        while (($line = fgets($this->socket, 515)) != false) {
            $response .= trim($line) . "\n";
            if (substr($line, 3, 1) == ' ') {
                break;
            }
        }
        return trim($response);
    }

    /**
     * Send command to mail server
     * @param $cmd
     * @return string
     */
    protected function sendCMD($cmd)
    {
        fputs($this->socket, $cmd . self::CRLF);
        return $this->getResponse();
    }

    /**
     * Format email address (with name)
     * @param $address
     * @return string
     */
    protected function formatAddress($address)
    {
        return ($address[1] == '') ? $address[0] : '"' . $address[1] . '" <' . $address[0] . '>';
    }

    /**
     * Format email address to list
     * @param $addresses
     * @return string
     */
    protected function formatAddressList($addresses)
    {
        $list = '';
        foreach ($addresses as $address) {
            if ($list) {
                $list .= ', ' . self::CRLF . "\t";
            }
            $list .= $this->formatAddress($address);
        }
        return $list;
    }
}
