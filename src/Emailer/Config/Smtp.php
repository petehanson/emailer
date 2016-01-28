<?php

namespace UAR\Emailer\Config;

class Smtp extends Base {

    public $host = 'localhost';
    public $port = 25;
    public $username = null;
    public $password = null;
    public $encryption = null;

    public function __construct() {
        parent::__construct();


        $this->setProperty($this->host,'emailer_smtp_host');
        $this->setProperty($this->port,'emailer_smtp_port');
        $this->setProperty($this->username,'emailer_smtp_username');
        $this->setProperty($this->password,'emailer_smtp_password');
        $this->setProperty($this->encryption,'emailer_smtp_encryption');

    }

    public function getTransport() {
        $transport = \Swift_SmtpTransport::newInstance($this->host,$this->port);

        if ($this->username) {
            $transport->setUsername($this->username);
        }

        if ($this->password) {
            $transport->setPassword($this->password);
        }

        if ($this->encryption) {
            $transport->setEncryption($this->encryption);
        }

        return $transport;
    }
}