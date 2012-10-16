<?php
namespace Fp\BadaBoomBundle\ChainNode\Provider;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request;

use BadaBoom\ChainNode\AbstractChainNode;
use BadaBoom\Context;

class RequestProvider extends AbstractChainNode implements EventSubscriberInterface
{
    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    public function handle(Context $context)
    {
        if ($this->request) {
            $context->setVar('server', $this->request->server->all());
            $context->setVar('attributes', $this->request->attributes->all());
            $context->setVar('cookies', $this->request->cookies->all());
            $context->setVar('query', $this->request->query->all());
            $context->setVar('request', $this->request->request->all());
        }

        $this->handleNextNode($context);
    }

    public function onEarlyKernelRequest(GetResponseEvent $event)
    {
        $this->setRequest($event->getRequest());
    }

    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    static public function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => array('onEarlyKernelRequest', 1000)
        );
    }
}