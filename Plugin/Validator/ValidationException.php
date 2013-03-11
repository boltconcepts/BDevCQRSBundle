<?php
namespace BDev\Bundle\CQRSBundle\Plugin\Validator;

use Exception;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Exception\ValidatorException;

class ValidationException extends ValidatorException
{
    protected $subject;

    protected $violations;

    public function __construct($subject, ConstraintViolationListInterface $violations, $code = 0, Exception $previous = null)
    {
        $this->subject = $subject;
        $this->violations = $violations;

        $msg = array(
            (is_object($subject) ? get_class($subject) : $subject) . ' contains violations:'
        );
        foreach ($this->violations as $violation) {
            /** @var $violation \Symfony\Component\Validator\ConstraintViolation */
            $msg[] = $violation->getPropertyPath() . ': ' . $violation->getMessage();
        }

        parent::__construct(join(PHP_EOL, $msg), $code, $previous);
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function getViolations()
    {
        return $this->violations;
    }
}
