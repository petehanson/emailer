<?php

namespace UAR;

class EmailerConfig {
    public $host = 'localhost';
    public $port = 25;
    public $username = null;
    public $password = null;
    public $messageConfigLocation = null;
    public $messageConfigExtension = "json";


    public function messagePath($messageName) {
        $path = $this->messageConfigLocation . DIRECTORY_SEPARATOR . $messageName . "." . $this->messageConfigExtension;
        return $path;
    }
}