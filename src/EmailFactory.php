<?php

namespace UAR;

use \UAR\Message;

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

}
