<?php
namespace BDev\Bundle\CQRSBundle\Tests\DependencyInjection;

use BDev\Bundle\CQRSBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaultConfig()
    {
        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration(), array(array()));

        $this->assertEquals(
            self::getBundleDefaultConfig(),
            $config
        );
    }

    protected static function getBundleDefaultConfig()
    {
        return array(
            'command_validation' => false
        );
    }
}