<?php
namespace Fp\BadaBoomBundle\ChainNode\Provider;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

use BadaBoom\ChainNode\Provider\AbstractProvider;
use BadaBoom\DataHolder\DataHolderInterface;

class SessionProvider extends AbstractProvider
{
    protected $sectionName;

    protected $session;

    public function __construct(SessionInterface $session, $sectionName = 'session')
    {
        $this->session = $session;
        $this->sectionName = $sectionName;
    }

    public function handle(\Exception $exception, DataHolderInterface $data)
    {
        $data->set($this->sectionName, $this->session->all());

        $this->handleNextNode($exception, $data);
    }
}