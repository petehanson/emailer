<?php

namespace UAR;

use \UAR\Message;
use \UAR\MessageConfig;
use \UAR\MessageInterface;
use \UAR\Config\Smtp;
use \UAR\Config\Sendmail;

use \UAR\Exception\MissingEnvironmentDriverException;


//TODO: Look to make parts of this abstract, so it has to be extended
class EmailerFactory {

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

        $message = new Message(\UAR\MessageConfig::load($path));
        return $message;
    }

    public static function send(\UAR\MessageInterface $message) {

        $config = self::config();
        $transport = $config->getTransport();

        $mailer = \Swift_Mailer::newInstance($transport);
        return $mailer->send($message);
    }
}
