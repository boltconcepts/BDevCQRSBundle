<?php
namespace BDev\Bundle\CQRSBundle\Plugin\Validator;

use LiteCQRS\Bus\MessageHandlerInterface;
use Symfony\Component\Validator\ValidatorInterface;

/**
 * Command handler that validates the given command.
 */
class ValidatorHandler implements MessageHandlerInterface
{
    private $validator;
    private $next;

    public function __construct(ValidatorInterface $validator, MessageHandlerInterface $next)
    {
        $this->validator = $validator;
        $this->next = $next;
    }

    public function handle($message)
    {
        $violation = $this->validator->validate($message);
        if ($violation->count() > 0) {
            throw new ValidationException($message, $violation);
        }

        $this->next->handle($message);
    }
}
