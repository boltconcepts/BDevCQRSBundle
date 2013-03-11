<?php
namespace BDev\Bundle\CQRSBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $tb = new TreeBuilder();

        $tb
            ->root('bdev_cqrs')
                ->children()
                    ->booleanNode('command_validation')->defaultFalse()->end()
                ->end();

        return $tb;
    }
}
