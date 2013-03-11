<?php
namespace BDev\Bundle\CQRSBundle\Tests\Bus;
use BDev\Bundle\CQRSBundle\Bus\ContainerScopedCommandBus;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\Scope;

class ContainerScopedCommandBusTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContainerScopedCommandBus
     */
    protected $bus;

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    protected function setUp()
    {
        $this->container = new Container();
        $this->container->addScope(new Scope('command'));
        $this->bus = new ContainerScopedCommandBus($this->container);
    }

    protected function tearDown()
    {
        $this->container = null;
        $this->bus = null;
    }

    public function testExecute()
    {
        $this->assertFalse($this->container->isScopeActive('command'));

        $command = $this->getMock('LiteCQRS\Command', array(), array(), 'testHandleCommand');
        $handler = $this->getMock('stdClass', array('testHandle'));
        $handler->expects($this->once())->method('testHandle')->with($command)->will($this->returnValue('handled'));
        $this->container->set('testCommandHandler', $handler);

        $this->bus->registerServices(array('testHandleCommand' => 'testCommandHandler'));

        $rtn = $this->bus->execute($command);

        $this->assertFalse($this->container->isScopeActive('command'));
        $this->assertEquals('handled', $rtn);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage A command cannot be fired into the service layer while it's active. Did you mean to use the `handle`?
     */
    public function testExecuteTwice()
    {
        $this->assertFalse($this->container->isScopeActive('command'));

        $bus = $this->bus;
        $command = $this->getMock('LiteCQRS\Command', array(), array(), 'testHandleCommand');
        $handler = $this->getMock('stdClass', array('testHandle'));
        $handler->expects($this->once())->method('testHandle')->with($command)->will($this->returnCallback(function() use ($bus, $command){ $bus->execute($command); }));
        $this->container->set('testCommandHandler', $handler);

        $this->bus->registerServices(array('testHandleCommand' => 'testCommandHandler'));

        $this->bus->execute($command);
    }

    public function testHandle()
    {
        $command = $this->getMock('LiteCQRS\Command', array(), array(), 'testHandleCommand');
        $handler = $this->getMock('stdClass', array('testHandle'));
        $handler->expects($this->once())->method('testHandle')->with($command);
        $this->container->set('testCommandHandler', $handler);
        $this->container->enterScope('command');

        $this->bus->registerServices(array('testHandleCommand' => 'testCommandHandler'));
        $this->bus->handle($command);

        $this->assertAttributeEquals(false, 'executing', $this->bus);
    }

    /**
     * @expectedException \BDev\Bundle\CQRSBundle\Exception\InactiveScopeException
     */
    public function testHandleInvalidScope()
    {
        $command = $this->getMock('LiteCQRS\Command', array(), array(), 'testHandleCommand');
        $this->bus->handle($command);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Expected `command` to be a instance of `LiteCQRS\Command`.
     */
    public function testHandleInvalidCommand()
    {
        $this->container->enterScope('command');
        $this->bus->handle(new \stdClass());
    }

    public function testHandleWithHandlerException()
    {
        $command = $this->getMock('LiteCQRS\Command', array(), array(), 'testHandleCommand');
        $this->container->set('testCommandHandler', new \stdClass());
        $this->container->enterScope('command');

        $this->bus->registerServices(array('testHandleCommand' => 'testCommandHandler'));

        try {
            $this->bus->handle($command);
            $this->fail();
        } catch (\RuntimeException $e) {
            $this->assertEquals("Service stdClass has no method testHandle to handle command.", $e->getMessage());
            $this->assertAttributeEquals(false, 'executing', $this->bus);
        }
    }
}