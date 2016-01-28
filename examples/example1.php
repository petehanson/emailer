<?php
/**
 * Created by PhpStorm.
 * User: peteh
 * Date: 1/28/16
 * Time: 12:55 PM
 */

require_once("../vendor/autoload.php");
use \UAR\Emailer\Factory as EmailerFactory;

$_ENV['emailer_driver'] = 'smtp';
$_ENV['emailer_smtp_host'] = 'localhost';
$_ENV['emailer_smtp_post'] = 1025;
$_ENV['emailer_message_location'] = __DIR__;  // use the current folder that this example file is in

try {
    $message = EmailerFactory::message("example1");
    $result = EmailerFactory::send($message);
} catch (Exception $e) {
    var_dump($e);
}



