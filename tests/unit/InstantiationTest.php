<?php


class InstantiationTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    public function testInstantiation()
    {
        $emailerConfig = new \UAR\EmailerConfig();
        $this->assertInstanceOf("\UAR\EmailerConfig",$emailerConfig);

        $email = new \UAR\Message(\UAR\MessageConfig::load("tests/_data/testemail1.json"));
        $this->assertInstanceOf("\UAR\Message",$email);
        $this->assertInstanceOf("\UAR\MessageInterface",$email);

        $factory = new \UAR\EmailerFactory();
        $this->assertInstanceOf("\UAR\EmailFactory",$factory);
    }
}