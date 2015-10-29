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
    protected $ccbcc = null;

    protected function _before()
    {
        $dataPath = dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "_data" . DIRECTORY_SEPARATOR;

        $this->file1 = $dataPath . "testemail1.json";
        $this->file2 = $dataPath . "testemail2.json";
        $this->emptyFile = $dataPath . "empty.json";
        $this->simpleFile = $dataPath . "simpletest.json";
        $this->fromObject = $dataPath . "fromobject.json";
        $this->fileNotFound = $dataPath . "doesnotexist.json";
        $this->ccbcc = $dataPath . "testccbcc.json";
    }

    protected function _after()
    {
    }


    // tests
    public function testEmail1() {
        $message = new \UAR\Message(\UAR\MessageConfig::load($this->file1));
        $this->assertEquals("This is test email 1 subject line",$message->getSubject());
        $this->assertEquals("This is the test email 1 body",$message->getBody());

        $from = $message->getFrom();
        $emails = array_keys($from);
        $email = $emails[0];
        $name = $from[$email];

        $this->assertEquals("sender@example.com",$email);
        $this->assertEquals("Sender Name",$name);


        $recipients = $message->getTo();
        $emails= array_keys($recipients);
        $to1 = $emails[0];
        $to2 = $emails[1];

        $this->assertEquals("person1@example.com",$to1);
        $this->assertEquals("person2@example.com",$to2);

    }


    public function testEmail2() {
        $message = new \UAR\Message(\UAR\MessageConfig::load($this->file2));
        $message->replace("replace1","test1");
        $message->replace("replace2","test2");
        $message->replace("replace3","person2@example.com");
        $message->replace("replace4","sender@example.com");

        $this->assertEquals("This is test email 2 subject line test1",$message->getSubject());
        $this->assertEquals("This is the test email 2 body test2",$message->getBody());


        $recipients = $message->getTo();
        $emails= array_keys($recipients);
        $to1 = $emails[0];
        $to2 = $emails[1];

        $this->assertEquals("person1@example.com",$to1);
        $this->assertEquals("Person One",$recipients[$to1]);
        $this->assertEquals("person2@example.com",$to2);
        $this->assertEquals("Person Two",$recipients[$to2]);

        $from = $message->getFrom();
        $emails = array_keys($from);
        $email = $emails[0];
        $name = $from[$email];

        $this->assertEquals("sender@example.com",$email);
        $this->assertEquals("Sender Name",$name);
    }

    public function testSimpleEmail() {
        $message = new \UAR\Message(\UAR\MessageConfig::load($this->simpleFile));

        $this->assertEquals("This is simple email subject line",$message->getSubject());
        $this->assertEquals("This is a simple email test",$message->getBody());

        $from = $message->getFrom();
        $emails = array_keys($from);
        $email = $emails[0];

        $this->assertEquals("sender@example.com",$email);

        $recipients = $message->getTo();
        $emails= array_keys($recipients);
        $to = $emails[0];

        $this->assertEquals("recipient@example.com",$to);

    }

    public function testFromObject() {
        $message = new \UAR\Message(\UAR\MessageConfig::load($this->fromObject));

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
    public function testInvalidJson() {
        $message = new \UAR\Message(null);
    }

    public function testCcBcc() {

        $originalJson = \UAR\MessageConfig::load($this->ccbcc);

        $message = new \UAR\Message($originalJson);

        $message->replace("replace1","test1");
        $message->replace("replace2","test2");
        $message->replace("replace3","person2@example.com");
        $message->replace("replace4","sender@example.com");

        $from = $message->getFrom();
        $emails = array_keys($from);
        $email = $emails[0];
        $name = $from[$email];

        $this->assertEquals("sender@example.com",$email);
        $this->assertEquals("Sender Name",$name);

        $ccs = $message->getCc();
        $emails = array_keys($ccs);
        $email = $emails[0];

        $this->assertEquals("cc1@example.com",$email);

        $bccs = $message->getBcc();
        $emails = array_keys($bccs);
        $email = $emails[0];

        $this->assertEquals("bcc1@example.com",$email);

        // test the CC system with the alternate CC recipient format
        $obj = json_decode($originalJson);
        $cc1 = new stdClass();
        $cc1->email = "cc1@example.com";
        $cc1->name = "CC One";
        $obj->cc = array($cc1);
        $json = json_encode($obj);

        $message = new \UAR\Message($json);
        $message->replace("replace1","test1");
        $message->replace("replace2","test2");
        $message->replace("replace3","person2@example.com");
        $message->replace("replace4","sender@example.com");

        $ccs = $message->getCc();
        $emails = array_keys($ccs);
        $email = $emails[0];

        $this->assertEquals("cc1@example.com",$email);
        $this->assertEquals("CC One",$ccs[$email]);



    }



}