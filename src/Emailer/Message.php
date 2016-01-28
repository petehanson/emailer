<?php

namespace UAR\Emailer;

use Swift_Message;
use Mustache_Engine;

class Message extends \Swift_Message implements \UAR\Emailer\MessageInterface {

    protected $data;
    protected $replacements = array();
    protected $openingTag = '{{';
    protected $closingTag = '}}';

    protected $m;

    public function __construct($json) {

        $this->data = json_decode($json);

        if ($this->data === false || $this->data === null) {
            throw new \Exception("JSON passed to the Message object was invalid");
        }


        $subject = null;
        $body = null;
        $contentType = null;
        $charset = null;

        parent::__construct($subject,$body,$contentType,$charset);
        //public function __construct($subject = null, $body = null, $contentType = null, $charset = null)

        $this->setupInitialData();

        $this->m = new \Mustache_Engine;

        $this->performReplacement();


    }

    protected function setupInitialData() {

        if (isset($this->data->tagOpening)) {
            $this->openingTag = $this->data->tagOpening;
        }

        if (isset($this->data->tagClosing)) {
            $this->closingTag = $this->data->tagClosing;
        }



        if (isset($this->data->tags)) {
            $tags = $this->data->tags;
            if (!is_array($tags)) {
                $tags = array();
            }

            foreach ($tags as $tag) {
                $this->replacements[$tag] = "";
            }
        }

    }

    protected function performReplacement() {


        //echo $m->render('Hello, {{ planet }}!', array('planet' => 'world')); // Hello, world!

        // get parameters from the config
        $tos = $this->buildRecipients((isset($this->data->to) ? $this->data->to : null));
        $ccs = $this->buildRecipients((isset($this->data->cc) ? $this->data->cc : null));
        $bccs = $this->buildRecipients((isset($this->data->bcc) ? $this->data->bcc : null));
        $from = $this->buildFrom();
        $subject = $this->buildSubject();
        $bodies = $this->buildBodies();


        // do any replacements
        $tos = $this->replaceRecipients($tos);
        $ccs = $this->replaceRecipients($ccs);
        $bccs = $this->replaceRecipients($bccs);
        $from = $this->replaceFrom($from);
        $subject = $this->replaceSubject($subject);
        $bodies = $this->replaceBodies($bodies);


        // set values on Message object
        if ($subject !== null) {
            $this->setSubject($subject);
        }

        if ($bodies !== null) {
            $bodyCount = 0;
            foreach ($bodies as $body) {
                if ($bodyCount == 0) {
                    $this->setBody($body['content'],$body['type']);
                } else {
                    $this->addPart($body['content'],$body['type']);
                }

                $bodyCount++;
            }
        }

        if ($from !== null) {
            $this->setFrom($from);
        }

        if ($tos !== null) {
            $this->setTo($tos);
        }

        if ($ccs !== null) {
            $this->setCc($ccs);
        }

        if ($bccs !== null) {
            $this->setBcc($bccs);
        }

    }

    public function replace($key,$value) {
        $this->replacements[$key] = $value;
        $this->performReplacement();
    }

    protected function buildSubject() {
        $subject = null;

        if (isset($this->data->subject) && is_string($this->data->subject)) {
            $subject = $this->data->subject;
        }
        return $subject;
    }

    protected function replaceSubject($subject) {
        if ($subject !== null) {

            $subject = $this->m->render($subject,$this->replacements);
        }

        return $subject;
    }

    protected function buildRecipients($field) {
        $recipients = null;

        if (isset($field) && is_string($field)) {
            $recipients = array();
            array_push($recipients,array("email"=>$field,"name"=>null));
        }

        if (isset($field) && is_array($field)) {
            $recipients = array();


            foreach ($field as $recipient) {
                if (is_string($recipient)) {
                    array_push($recipients,array("email"=>$recipient,"name"=>null));
                }

                if (is_object($recipient)) {
                    array_push($recipients,array("email"=>$recipient->email,"name"=>$recipient->name));
                }
            }
        }

        return $recipients;
    }

    protected function replaceRecipients($recipients) {

        if ($recipients !== null) {

            $newTo = array();

            foreach ($recipients as $recipient) {

                $email = $recipient['email'];
                $name = $recipient['name'];

                $email = $this->m->render($email,$this->replacements);
                $name = $this->m->render($name,$this->replacements);

                if ($email == "") {  // basically, we'll skip adding an item to the array if we don't have a valid email to use. This can happen on initialization.
                    continue;
                }

                if ($email && $name) {
                    $newTo[$email] = $name;
                } else {
                    array_push($newTo,$email);
                }

            }

            $recipients = $newTo;

            if (count($recipients) == 0) {
                $recipients = null;
            }
        }

        return $recipients;
    }

    protected function buildFrom() {

        $email = null;
        $name = null;

        if (isset($this->data->from) && is_string($this->data->from)) {
            $email = $this->data->from;
        }

        if (isset($this->data->from->email) && is_string($this->data->from->email)) {
            $email = $this->data->from->email;
        }

        if (isset($this->data->from->name) && is_string($this->data->from->name)) {
            $name = $this->data->from->name;
        }

        if (isset($this->data->fromEmail) && is_string($this->data->fromEmail)) {
            $email= $this->data->fromEmail;
        }

        if (isset($this->data->fromName) && is_string($this->data->fromName)) {
            $name= $this->data->fromName;
        }


        if ($email) {

            if ($name) {
               return array($email => $name);
            } else {
               return $email;
            }

        } else {
            return null;
        }
    }

    protected function replaceFrom($from) {

        if ($from !== null) {


            if (is_array($from)) {
                // from is email => name

                $newFrom = array();

                foreach ($from as $key=>$value) {
                    $key = $this->m->render($key,$this->replacements);
                    $value = $this->m->render($value,$this->replacements);

                    if ($key == "") {  // basically, we'll skip adding an item to the array if we don't have a valid email to use. This can happen on initialization.
                        continue;
                    }

                    $newFrom[$key] = $value;
                }

                $from = $newFrom;

                if (count($from) == 0) {
                    $from = null;
                }
            } else {
                // from is just an email string
                $from = $this->m->render($from,$this->replacements);
            }
        }

        return $from;
    }

    protected function buildBodies() {
        $bodies = null;

        if (isset($this->data->body) && is_string($this->data->body)) {
            $bodies = array();
            array_push($bodies,array("content"=>$this->data->body,"type"=>"text/html"));
        }

        if (isset($this->data->bodies) && is_array($this->data->bodies)) {
            $bodies = array();
            foreach ($this->data->bodies as $body) {
                array_push($bodies,array("content"=>$body->contents,"type"=>$body->contentType));
            }
        }

        return $bodies;
    }

    protected function replaceBodies($bodies) {
        if ($bodies !== null) {
            foreach ($bodies as &$body) {

                $body['content'] = $this->m->render($body['content'],$this->replacements);
            }
        }

        return $bodies;
    }

}