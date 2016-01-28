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
        $email = new \UAR\Emailer\Message(\UAR\Emailer\MessageConfig::load("tests/_data/testemail1.json"));
        $this->assertInstanceOf("\UAR\Emailer\Message",$email);
        $this->assertInstanceOf("\UAR\Emailer\MessageInterface",$email);

        $factory = new \UAR\Emailer\Factory();
        $this->assertInstanceOf("\UAR\Emailer\Factory",$factory);
    }
}