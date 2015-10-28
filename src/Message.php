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

        codecept_debug($this->data);

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
        $subject = $this->buildSubject();
        $bodies = array();

        if (isset($this->data->body)) {
            array_push($bodies,array("content"=>$this->data->body,"type"=>"text/html"));
        }

        if (isset($this->data->bodies)) {
            foreach ($this->data->bodies as $body) {
                array_push($bodies,array("content"=>$body->contents,"type"=>$body->contentType));
            }
        }


        foreach ($this->replacements as $key=>$value) {
            $searchString = $this->openingTag . $key . $this->closingTag;

            $subject = str_replace($searchString,$value,$subject);

            foreach ($bodies as &$body) {
                $body['content'] = str_replace($searchString,$value,$body['content']);
            }
        }

        codecept_debug($bodies);

        $this->setSubject($subject);

        foreach ($bodies as $body) {
            $this->setBody($body['content'],$body['type']);
        }



        $from = $this->buildFrom();
        if ($from) {
            $this->setFrom($from);
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

}