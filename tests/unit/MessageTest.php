<?php


class MessageTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected $file1 = null;
    protected $file2 = null;
    protected $simpleFile = null;
    protected $fromObject = null;
    protected $emptyFile = null;
    protected $fileNotFound = null;

    protected function _before()
    {
        $dataPath = dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "_data" . DIRECTORY_SEPARATOR;

        $this->file1 = $dataPath . "testemail1.json";
        $this->file2 = $dataPath . "testemail2.json";
        $this->emptyFile = $dataPath . "empty.json";
        $this->simpleFile = $dataPath . "simpletest.json";
        $this->fromObject = $dataPath . "fromobject.json";
        $this->fileNotFound = $dataPath . "doesnotexist.json";
    }

    protected function _after()
    {
    }

    /**
     * @expectedException Exception
     */
    public function testEmptyJson() {

        $message = new \UAR\Message($this->emptyFile);
    }

    // tests
    public function testEmail1() {
        $message = new \UAR\Message($this->file1);
        $this->assertEquals("This is test email 1 subject line",$message->getSubject());
        $this->assertEquals("This is the test email 1 body",$message->getBody());

        $from = $message->getFrom();
        $emails = array_keys($from);
        $email = $emails[0];
        $name = $from[$email];

        $this->assertEquals("sender@example.com",$email);
        $this->assertEquals("Sender Name",$name);

    }


    public function testEmail2() {
        $message = new \UAR\Message($this->file2);
        $message->replace("replace1","test1");
        $message->replace("replace2","test2");
        $this->assertEquals("This is test email 2 subject line test1",$message->getSubject());
        $this->assertEquals("This is the test email 2 body test2",$message->getBody());
    }

    public function testSimpleEmail() {
        $message = new \UAR\Message($this->simpleFile);

        $this->assertEquals("This is simple email subject line",$message->getSubject());
        $this->assertEquals("This is a simple email test",$message->getBody());

        $from = $message->getFrom();
        $emails = array_keys($from);
        $email = $emails[0];

        $this->assertEquals("sender@example.com",$email);

    }

    public function testFromObject() {
        $message = new \UAR\Message($this->fromObject);

        $from = $message->getFrom();
        $emails = array_keys($from);
        $email = $emails[0];
        $name = $from[$email];

        $this->assertEquals("sender@example.com",$email);
        $this->assertEquals("Sender Person",$name);

    }


    /**
     * @expectedException Exception
     */
    public function testConfigNotFoundException() {
        $message = new \UAR\Message($this->fileNotFound);
    }

}