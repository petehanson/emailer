<?php

namespace UAR\Emailer\Config;

class Sendmail extends Base {

    public $binary = '/usr/bin/sendmail -bs';

    public function __construct() {

        parent::__construct();

        $this->setProperty($this->binary,"emailer_sendmail_binary");
    }

    public function getTransport() {
        $transport = \Swift_SendmailTransport::newInstance($this->binary);
        return $transport;
    }
}