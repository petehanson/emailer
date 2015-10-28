<?php

namespace UAR;

class Message extends \Swift_Message {

    protected $data;
    protected $replacements = array();
    protected $openingTag = '{{';
    protected $closingTag = '}}';


    public function __construct($messageConfigPath) {

        if (!file_exists($messageConfigPath)) {
            throw new \Exception("JSON config {$messageConfigPath} not found");
        }

        $json = file_get_contents($messageConfigPath);
        if (!$json) {
            throw new \Exception("JSON config {$messageConfigPath} is empty");
        }

        $this->data = json_decode($json);

        $subject = null;
        $body = null;
        $contentType = null;
        $charset = null;

        parent::__construct($subject,$body,$contentType,$charset);
        //public function __construct($subject = null, $body = null, $contentType = null, $charset = null)

        $this->setupInitialData();
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

        // get parameters from the config
        $recipients = $this->buildTo();
        $from = $this->buildFrom();
        $subject = $this->buildSubject();
        $bodies = $this->buildBodies();

        // do any replacements
        foreach ($this->replacements as $key=>$value) {
            $searchString = $this->openingTag . $key . $this->closingTag;

            $recipients = $this->replaceTo($recipients,$searchString,$value);
            $from = $this->replaceFrom($from,$searchString,$value);
            $subject = $this->replaceSubject($subject,$searchString,$value);
            $bodies = $this->replaceBodies($bodies,$searchString,$value);

        }


        // set values on Message object
        if ($subject !== null) {
            $this->setSubject($subject);
        }

        if ($bodies !== null) {
            foreach ($bodies as $body) {
                $this->setBody($body['content'],$body['type']);
            }
        }

        if ($from !== null) {
            $this->setFrom($from);
        }

        if ($recipients !== null) {
            $this->setTo($recipients);
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

    protected function replaceSubject($subject,$searchString,$replaceValue) {
        if ($subject !== null) {
            $subject = str_replace($searchString,$replaceValue,$subject);
        }

        return $subject;
    }

    protected function buildTo() {
        $recipients = null;

        if (isset($this->data->to) && is_string($this->data->to)) {
            $recipients = array();
            array_push($recipients,$this->data->to);
        }

        if (isset($this->data->to) && is_array($this->data->to)) {
            $recipients = array();


            foreach ($this->data->to as $recipient) {
                if (is_string($recipient)) {
                    array_push($recipients,$recipient);
                }

                if (is_object($recipient)) {
                    $recipients[$recipient->email] = $recipient->name;
                }
            }
        }

        return $recipients;
    }

    protected function replaceTo($recipients,$searchString,$replaceValue) {

        if ($recipients !== null) {

            $newTo = array();

            foreach ($recipients as $key=>$value) {
                $key = str_replace($searchString,$replaceValue,$key);
                $value = str_replace($searchString,$replaceValue,$value);

                if ($key == "") {  // basically, we'll skip adding an item to the array if we don't have a valid email to use. This can happen on initialization.
                    continue;
                }

                $newTo[$key] = $value;
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
               return array($email);
            }

        } else {
            return null;
        }
    }

    protected function replaceFrom($from,$searchString,$replaceValue) {

        if ($from !== null) {

            $newFrom = array();

            foreach ($from as $key=>$value) {
                $key = str_replace($searchString,$replaceValue,$key);
                $value = str_replace($searchString,$replaceValue,$value);

                if ($key == "") {  // basically, we'll skip adding an item to the array if we don't have a valid email to use. This can happen on initialization.
                    continue;
                }

                $newFrom[$key] = $value;
            }

            $from = $newFrom;

            if (count($from) == 0) {
                $from = null;
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

    protected function replaceBodies($bodies,$searchString,$replaceValue) {
        if ($bodies !== null) {
            foreach ($bodies as &$body) {
                $body['content'] = str_replace($searchString,$replaceValue,$body['content']);
            }
        }

        return $bodies;
    }

}