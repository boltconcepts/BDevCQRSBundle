<?php
namespace BDev\Bundle\CQRSBundle;

use BDev\Bundle\CQRSBundle\DependencyInjection\BDevCQRSExtension;
use Symfony\Component\DependencyInjection\Scope;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;

class BDevCQRSBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addScope(new Scope(Bus\ContainerScopedCommandBus::CONTAINER_SCOPE));
    }

    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new BDevCQRSExtension();
        }
        return $this->extension;
    }
}
