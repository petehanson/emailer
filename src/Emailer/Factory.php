<?php

namespace UAR\Emailer;

use \UAR\Emailer\Message;
use \UAR\Emailer\MessageConfig;
use \UAR\Emailer\Config\Smtp;
use \UAR\Emailer\Config\Sendmail;

use \UAR\Emailer\Exception\MissingEnvironmentDriverException;

class Factory {

    public static function config() {
        $driver = (isset($_ENV['emailer_driver'])) ? $_ENV['emailer_driver'] : false;
        if ($driver === false) {
            throw new MissingEnvironmentDriverException();
        }

        switch($driver) {
            case "sendmail":
                $config = new Sendmail();
                break;

            case "smtp":
            default:
                $config = new Smtp();
                break;
        }

        return $config;
    }

    public static function message($messageName) {
        $config = self::config();
        $path = $config->messagePath($messageName);

        $message = new Message(MessageConfig::load($path));
        return $message;
    }

    public static function send(MessageInterface $message) {
        $config = self::config();
        $transport = $config->getTransport();

        $mailer = \Swift_Mailer::newInstance($transport);
        return $mailer->send($message);
        
    }
}
