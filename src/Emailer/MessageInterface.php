<?php

namespace UAR\Emailer;

interface MessageInterface {
    public function replace($key,$value);
}