<?php
namespace BDev\Bundle\CQRSBundle\Plugin\Validator;

use Symfony\Component\Validator\ValidatorInterface;

class ValidatorHandlerFactory
{
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function __invoke($handler)
    {
        return new ValidatorHandler($this->validator, $handler);
    }
}

