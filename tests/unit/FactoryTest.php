<?php


class FactoryTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    protected $testEmail1 = "testemail1";

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests
    public function testMessageReturn()
    {
        $message = \UAR\EmailFactory::message($this->testEmail1);
        $this->assertInstanceOf("\UAR\Message",$message);

    }
}