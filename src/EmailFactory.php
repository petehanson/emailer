<?php

namespace UAR;

use \UAR\Message;
use \UAR\MessageConfig;
use \UAR\MessageInterface;
use \UAR\EmailerConfig;

//TODO: Look to make parts of this abstract, so it has to be extended
class EmailFactory {

    public static function config() {
        $config = new \UAR\EmailerConfig();
        $config->host = "localhost";
        $config->port = "1025";
        $config->messageConfigLocation = realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "tests" . DIRECTORY_SEPARATOR . "_data");

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

        $transport = \Swift_SmtpTransport::newInstance($config->host,$config->port);

        if ($config->username) {
            $transport->setUsername($config->username);
        }

        if ($config->password) {
            $transport->setPassword($config->password);
        }

        if ($config->encryption) {
            $transport->setEncryption($config->encryption);
        }

        $mailer = \Swift_Mailer::newInstance($transport);
        return $mailer->send($message);
    }
}
