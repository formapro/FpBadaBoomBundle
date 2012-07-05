<?php
namespace Fp\BadaBoomBundle\ChainNode\Provider;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request;

use BadaBoom\ChainNode\AbstractChainNode;
use BadaBoom\DataHolder\DataHolderInterface;

class RequestProvider extends AbstractChainNode implements EventSubscriberInterface
{
    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    public function handle(\Exception $exception, DataHolderInterface $data)
    {
        if ($this->request) {
            $data->set('server', $this->request->server->all());
            $data->set('attributes', $this->request->attributes->all());
            $data->set('cookies', $this->request->cookies->all());
            $data->set('query', $this->request->query->all());
            $data->set('request', $this->request->request->all());
        }

        $this->handleNextNode($exception, $data);
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