<?php
namespace Fp\BadaBoomBundle\EventListener;

use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleExceptionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Fp\BadaBoomBundle\ExceptionCatcher\ExceptionCatcherInterface;

class ConsoleExceptionListener implements EventSubscriberInterface
{
    /**
     * @var ExceptionCatcherInterface
     */
    protected $exceptionCatcher;

    /**
     * @param ExceptionCatcherInterface $exceptionCatcher
     */
    public function __construct(ExceptionCatcherInterface $exceptionCatcher)
    {
        $this->exceptionCatcher = $exceptionCatcher;
    }

    /**
     * @param ConsoleExceptionEvent $event
     */
    public function onConsoleException(ConsoleExceptionEvent $event)
    {
        $this->exceptionCatcher->handleException($event->getException());
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            ConsoleEvents::EXCEPTION => 'onConsoleException'
        );
    }
}