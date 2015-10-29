<?php


class MessageConfigLoaderTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected $simpleFile = null;
    protected $emptyFile = null;
    protected $fileNotFound = null;

    protected function _before()
    {
        $dataPath = dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "_data" . DIRECTORY_SEPARATOR;

        $this->emptyFile = $dataPath . "empty.json";
        $this->simpleFile = $dataPath . "simpletest.json";
        $this->fileNotFound = $dataPath . "doesnotexist.json";
    }

    protected function _after()
    {
    }

    /**
     * @expectedException Exception
     */
    public function testEmptyJson() {

        $json = \UAR\MessageConfig::load($this->emptyFile);
    }

    /**
     * @expectedException Exception
     */
    public function testConfigNotFoundException() {
        $json = \UAR\MessageConfig::load($this->fileNotFound);
    }

    // tests
    public function testSimpleFile()
    {
        $json = \UAR\MessageConfig::load($this->simpleFile);

        $data = json_decode($json);

        $this->assertEquals("This is simple email subject line", $data->subject);
    }
}