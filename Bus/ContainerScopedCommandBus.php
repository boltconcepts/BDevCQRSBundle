<?php
namespace BDev\Bundle\CQRSBundle\Bus;

use BDev\Bundle\CQRSBundle\Exception\InactiveScopeException;
use LiteCQRS\Command;
use LiteCQRS\Plugin\SymfonyBundle\ContainerCommandBus;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ContainerScopedCommandBus extends ContainerCommandBus
{
    const CONTAINER_SCOPE = 'command';

    /**
     * @var ContainerInterface
     */
    protected $container;

    protected $executing = false;

    public function __construct(ContainerInterface $container, array $proxyFactories = array())
    {
        parent::__construct($container, $proxyFactories);
        $this->container = $container;
    }

    /**
     * Execute a command in the current bus and return the command handlers result.
     * This method can only be called if the CommandBus is not executing any other commands.
     *
     * @param Command $command
     * @throws \LogicException
     * @throws \Exception
     * @return mixed
     */
    public function execute(Command $command)
    {
        if ($this->container->isScopeActive(self::CONTAINER_SCOPE)) {
            throw new \RuntimeException('A command cannot be fired into the service layer while it\'s active. Did you mean to use the `handle`?');
        }
        $this->container->enterScope(self::CONTAINER_SCOPE);

        try {
            // Resolve service for dispatching
            $type    = get_class($command);
            $service = $this->getService($type);

            // Create command handlers
            $invocationHandler = new CommandInvocationHandler($service);
            /** @var $handler \LiteCQRS\Bus\MessageHandlerInterface */
            $handler = $this->proxyHandler($invocationHandler);

            // Execute/handle command
            $handler->handle($command);

            $this->container->leaveScope(self::CONTAINER_SCOPE);

            return $invocationHandler->getResult();
        } catch(\Exception $e) {
            $this->container->leaveScope(self::CONTAINER_SCOPE);
            throw $e;
        }
    }

    /**
     * {@inheritDoc}
     *
     * @param Command $command
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function handle($command)
    {
        if (!is_object($command) || !($command instanceof Command)) {
            throw new \InvalidArgumentException("Expected `command` to be a instance of `LiteCQRS\\Command`.");
        }
        if (!$this->container->isScopeActive(self::CONTAINER_SCOPE)) {
            throw new InactiveScopeException('A command cannot be handled while the service layer isn\'t active. Did you mean to use `execute`?');
        }

        parent::handle($command);
    }
}