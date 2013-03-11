<?php
namespace BDev\Bundle\CQRSBundle\Tests;

use BDev\Bundle\CQRSBundle\BDevCQRSBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class BDevCQRSBundleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var BDevCQRSBundle
     */
    protected $bundle;

    protected function setUp()
    {
        $this->bundle = new BDevCQRSBundle();
    }

    protected function tearDown()
    {
        $this->bundle = null;
    }

    public function testBuild()
    {
        $container = new ContainerBuilder();
        $this->assertFalse($container->hasScope('command'));

        $this->bundle->build($container);
        $this->assertTrue($container->hasScope('command'));
    }
}