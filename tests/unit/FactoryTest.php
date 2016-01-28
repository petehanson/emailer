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
        $_ENV['emailer_driver'] = 'smtp';
        $_ENV['emailer_message_location'] = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '_data');

        $_ENV['emailer_smtp_host'] = 'localhost';
        $_ENV['emailer_smtp_port'] = '1025';
    }

    protected function _after()
    {
    }

    public function testFactoryConfig() {
        $config = \UAR\EmailerFactory::config();
        $this->assertInstanceOf("\UAR\Config\Smtp",$config);
    }

    // tests
    public function testMessageReturn()
    {
        $message = \UAR\EmailerFactory::message($this->testEmail1);
        $this->assertInstanceOf("\UAR\Message",$message);

    }

    public function testMessageSend() {

        $message = \UAR\EmailerFactory::message($this->testEmail1);
        $this->assertInstanceOf("\UAR\Message",$message);

        $result = \UAR\EmailerFactory::send($message);
        $this->assertEquals(2,$result);
    }

    /**
     * @expectedException \UAR\Exception\MissingEnvironmentDriverException
     */
    public function testMissingEnvironmentDriverException() {

        unset($_ENV['emailer_driver']);

        $config = \UAR\EmailerFactory::config();
    }
}