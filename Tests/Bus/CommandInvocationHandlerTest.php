<?php
namespace BDev\Bundle\CQRSBundle\Tests\Bus;

use BDev\Bundle\CQRSBundle\Bus\CommandInvocationHandler;

class CommandInvocationHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $service;

    /**
     * @var CommandInvocationHandler
     */
    protected $handler;

    protected function setUp()
    {
        $this->service = $this->getMock('stdClass', array('test'), array(), 'TestService');
        $this->handler = new CommandInvocationHandler($this->service);
    }

    protected function tearDown()
    {
        $this->service = null;
        $this->handler = null;
    }

    public function testHandle()
    {
        $command = $this->getMock('LiteCQRS\\Command', array(), array(), 'TestCommand');

        $this->service->expects($this->once())->method('test')->with($command)->will($this->returnValue('handled'));

        $this->handler->handle($command);

        $this->assertEquals('handled', $this->handler->getResult());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Expected `command` to be a instance of `LiteCQRS\Command`.
     */
    public function testHandleWithStringCommand()
    {
        $this->handler->handle('');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Expected `command` to be a instance of `LiteCQRS\Command`.
     */
    public function testHandleWithInvalidCommand()
    {
        $this->handler->handle(new \stdClass());
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Service TestService has no method testInvalid to handle command.
     */
    public function testHandleWithMissingMethod()
    {
        $command = $this->getMock('LiteCQRS\\Command', array(), array(), 'TestInvalidCommand');
        $this->handler->handle($command);
    }
}