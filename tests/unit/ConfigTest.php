<?php


class ConfigTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    protected $messageLocation;

    protected function _before()
    {
        $this->messageLocation = realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "_data");

        if ($this->messageLocation === false) {
            throw new exception("email config folder does not exist or resolve");
        }
    }

    protected function _after()
    {
    }

    // tests
    public function testSmtpConfig() {
        $_ENV['emailer_driver'] = 'smtp';
        $_ENV['emailer_smtp_host'] = 'localhost';
        $_ENV['emailer_smtp_port'] = '1025';
        $_ENV['emailer_smtp_username'] = 'user';
        $_ENV['emailer_smtp_password'] = 'pass';
        $_ENV['emailer_smtp_encryption'] = 'ssl';

        $_ENV['emailer_message_location'] = $this->messageLocation;

        $config = new \UAR\Config\Smtp();
        $this->assertInstanceOf("\UAR\Config\Smtp",$config);

        // test parameters
        $this->assertEquals("localhost",$config->host);
        $this->assertEquals("1025",$config->port);
        $this->assertEquals("user",$config->username);
        $this->assertEquals("pass",$config->password);
        $this->assertEquals("ssl",$config->encryption);

        // test message location
        $messageName = "testemail1";
        $path = $config->messagePath($messageName);
        $this->assertEquals(realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "_data" . DIRECTORY_SEPARATOR . "testemail1.json"),$path);

        // test transport
        $transport = $config->getTransport();
        $this->assertInstanceOf("\Swift_SmtpTransport",$transport);
    }

    public function testSendmailConfig() {
        $_ENV['emailer_driver'] = 'sendmail';
        $_ENV['emailer_sendmail_binary'] = '/usr/bin/sendmail';

        $_ENV['emailer_message_location'] = $this->messageLocation;

        $config = new \UAR\Config\Sendmail();
        $this->assertInstanceOf("\UAR\Config\Sendmail",$config);

        // test parameters
        $this->assertEquals("/usr/bin/sendmail",$config->binary);

        // test message location
        $messageName = "testemail1";
        $path = $config->messagePath($messageName);
        $this->assertEquals(realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "_data" . DIRECTORY_SEPARATOR . "testemail1.json"),$path);

        // test transport
        $transport = $config->getTransport();
        $this->assertInstanceOf("\Swift_SendmailTransport",$transport);
    }
}