<?php
namespace BDev\Bundle\CQRSBundle\Bus;

use LiteCQRS\Bus\CommandInvocationHandler as BaseCommandInvocationHandler;
use LiteCQRS\Command;

class CommandInvocationHandler extends BaseCommandInvocationHandler
{
    protected $service;

    protected $result = null;

    public function __construct($service)
    {
        parent::__construct($service);
        $this->service = $service;
    }

    /**
     * @param Command $command
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function handle($command)
    {
        if (!is_object($command) || !($command instanceof Command)) {
            throw new \InvalidArgumentException("Expected `command` to be a instance of `LiteCQRS\\Command`.");
        }

        $method  = $this->getHandlerMethodName($command);

        if (!method_exists($this->service, $method)) {
            throw new \RuntimeException("Service " . get_class($this->service) . " has no method " . $method . " to handle command.");
        }

        $this->result = $this->service->$method($command);
    }

    public function getResult()
    {
        return $this->result;
    }
}