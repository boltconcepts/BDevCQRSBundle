<?php
namespace BDev\Bundle\CQRSBundle\Tests\DependencyInjection;

use BDev\Bundle\CQRSBundle\DependencyInjection\BDevCQRSExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

abstract class FixtureBDevRoutingExtraExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testLoadDefaults()
    {
        $container = $this->getContainer('empty');

        $this->assertEquals('BDev\Bundle\CQRSBundle\Bus\ContainerScopedCommandBus', $container->getParameter('litecqrs.command_bus.class'));

        $this->assertFalse($container->hasDefinition('bdev.cqrs.plugin.command_validator_factory'));
    }

    public function testLoadWithValidatorPlugin()
    {
        $container = $this->getContainer('validator');
        $this->assertTrue($container->hasDefinition('bdev.cqrs.plugin.command_validator_factory'));
    }

    protected function getContainer($fixture)
    {
        $extension = new BDevCQRSExtension();

        $container = new ContainerBuilder(new ParameterBag(array(
            'kernel.debug' => true,
            'kernel.root_dir' => __DIR__,
            'kernel.cache_dir' => __DIR__,
        )));

        $container->registerExtension($extension);

        $container->addDefinitions(array('validator' => new Definition('Symfony\Component\Validator\ValidatorInterface')));

        $container->loadFromExtension($extension->getAlias());

        $this->loadFixture($container, $fixture);

        $container->compile();

        return $container;
    }

    abstract protected function loadFixture(ContainerBuilder $container, $fixture);
}