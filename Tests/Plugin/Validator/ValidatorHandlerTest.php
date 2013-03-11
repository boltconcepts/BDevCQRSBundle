<?php
namespace BDev\Bundle\CQRSBundle\Tests\Plugin\Validator;

use BDev\Bundle\CQRSBundle\Plugin\Validator\ValidatorHandler;
use Symfony\Component\Validator\ConstraintViolationList;

class ValidatorHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $nextHandler;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $validator;

    /**
     * @var ValidatorHandler
     */
    protected $handler;

    protected function setUp()
    {
        $this->validator = $this->getMock('Symfony\Component\Validator\ValidatorInterface');
        $this->nextHandler = $this->getMock('LiteCQRS\Bus\MessageHandlerInterface');
        $this->handler = new ValidatorHandler($this->validator, $this->nextHandler);
    }

    protected function tearDown()
    {
        $this->nextHandler = null;
        $this->validator = null;
        $this->handler = null;
    }

    public function testHandle()
    {
        $message = 'hello world';

        $violations = new ConstraintViolationList();
        $this->validator->expects($this->once())->method('validate')->with($message)->will($this->returnValue($violations));

        $this->nextHandler->expects($this->once())->method('handle')->with($message);

        $this->handler->handle($message);
    }

    /**
     * @expectedException \BDev\Bundle\CQRSBundle\Plugin\Validator\ValidationException
     */
    public function testHandleValidationFailed()
    {
        $message = 'hello world';

        $violations = new ConstraintViolationList(array($this->getMock('Symfony\Component\Validator\ConstraintViolationInterface')));
        $this->validator->expects($this->once())->method('validate')->with($message)->will($this->returnValue($violations));

        $this->nextHandler->expects($this->never())->method('handle');

        $this->handler->handle($message);
    }
}