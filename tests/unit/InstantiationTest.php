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
        /*
        $emailer = new \UAR\Emailer();
        $this->assertInstanceOf("\UAR\Emailer",$emailer);
        */

        $emailerConfig = new \UAR\EmailerConfig();
        $this->assertInstanceOf("\UAR\EmailerConfig",$emailerConfig);

        $email = new \UAR\Message("tests/_data/testemail1.json");
        $this->assertInstanceOf("\UAR\Message",$email);

        $factory = new \UAR\EmailFactory();
        $this->assertInstanceOf("\UAR\EmailFactory",$factory);
    }
}