<?php

namespace UAR\Emailer\Config;

abstract class Base {

    protected $driver;
    public $messageLocation;
    public $messageExtension = 'json';

    public function __construct() {
        $this->setDriver($_ENV['emailer_driver']);

        $this->messageLocation = $_ENV['emailer_message_location'];
    }

    public function setDriver($driver) {
        $this->driver = $driver;
    }

    public function messagePath($messageName) {
        $path = $this->messageLocation . DIRECTORY_SEPARATOR . $messageName . "." . $this->messageExtension;
        return $path;
    }


    protected function setProperty(&$variable,$key) {
        if (isset($_ENV[$key]) && $_ENV[$key]) {
            $variable = $_ENV[$key];
        }
    }

    abstract public function getTransport();
}