<?php
namespace Fp\BadaBoomBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

use BadaBoom\ChainNode\ChainNodeInterface;
use BadaBoom\DataHolder\DataHolder;

class ExceptionSubscriber implements EventSubscriberInterface
{
    protected $chain;

    public function __construct(ChainNodeInterface $chain)
    {
        $this->chain = $chain;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $this->chain->handle($event->getException(), new DataHolder());
    }

    static function getSubscribedEvents()
    {
        return array(
            KernelEvents::EXCEPTION => 'onKernelException'
        );
    }
}