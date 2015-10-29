<?php

namespace UAR;

class EmailerConfig {
    public $host = 'localhost';
    public $port = 25;
    public $username = null;
    public $password = null;
    public $encryption = null;
    public $transport = "mail";

    public $messageConfigLocation = null;
    public $messageConfigExtension = "json";


    public function messagePath($messageName) {
        $path = $this->messageConfigLocation . DIRECTORY_SEPARATOR . $messageName . "." . $this->messageConfigExtension;
        return $path;
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