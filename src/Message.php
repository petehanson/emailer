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
        $subject = $this->data->subject;
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


        // NEXT:  ADD IN support for from

        $from = array($this->data->fromEmail => $this->data->fromName);

        $this->setFrom($from);

    }

    public function replace($key,$value) {
        $this->replacements[$key] = $value;
        $this->performReplacement();
    }

}