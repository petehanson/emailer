<?php

namespace UAR\Emailer;

class MessageConfig {
    public static function load($path) {

        if (!file_exists($path)) {
            throw new \Exception("JSON config {$path} not found");
        }

        $json = file_get_contents($path);
        if (!$json) {
            throw new \Exception("JSON config {$path} is empty");
        }

        return $json;
    }
}
