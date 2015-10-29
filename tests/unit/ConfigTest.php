<?php


class ConfigTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected $config;

    protected function _before()
    {
        $config = new \UAR\EmailerConfig();
        $config->host = "localhost";
        $config->port = "25";
        $config->username = "user";
        $config->password = "pass";
        $config->transport = "smtp";
        $config->messageConfigLocation = realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "_data");

        $this->config = $config;
    }

    protected function _after()
    {
    }

    // tests
    public function testConfig()
    {
        $config = $this->config;
        codecept_debug($config->messageConfigLocation);


        $this->assertEquals("localhost",$config->host);
        $this->assertEquals("25",$config->port);
        $this->assertEquals("user",$config->username);
        $this->assertEquals("pass",$config->password);
        $this->assertEquals("smtp",$config->transport);
    }

    public function testDefaultConfig() {
        $config = new \UAR\EmailerConfig();

        $this->assertEquals("localhost",$config->host);
        $this->assertEquals("25",$config->port);
        $this->assertEquals("mail",$config->transport);
    }

    public function testMessageConfig() {
        $config = $this->config;

        $messageName = "testemail1";
        $path = $config->messagePath($messageName);

        $this->assertEquals(realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "_data" . DIRECTORY_SEPARATOR . "testemail1.json"),$path);
    }

    public function testTransports() {
        $config = $this->config;

        $transport = $config->getTransport();
        $this->assertInstanceOf("\Swift_SmtpTransport",$transport);
    }
}