<?php
namespace Fp\BadaBoomBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

use Fp\BadaBoomBundle\ExceptionCatcher\ExceptionCatcherInterface;

class ExceptionListener
{
    /**
     * @var \Fp\BadaBoomBundle\ExceptionCatcher\ExceptionCatcherInterface
     */
    protected $exceptionCatcher;

    /**
     * @param \Fp\BadaBoomBundle\ExceptionCatcher\ExceptionCatcherInterface $exceptionCatcher
     */
    public function __construct(ExceptionCatcherInterface $exceptionCatcher)
    {
        $this->exceptionCatcher = $exceptionCatcher;
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $this->exceptionCatcher->handleException($event->getException());
    }
}