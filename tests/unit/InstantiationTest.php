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
        $emailer = new \UAR\Emailer();
        $this->assertInstanceOf("\UAR\Emailer",$emailer);
    }
}