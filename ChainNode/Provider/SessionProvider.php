<?php
namespace Fp\BadaBoomBundle\ChainNode\Provider;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

use BadaBoom\ChainNode\Provider\AbstractProvider;
use BadaBoom\Context;

/**
 * @author Kotlyar Maksim <kotlyar.maksim@gmail.com>
 * @since 4/10/12
 */
class SessionProvider extends AbstractProvider
{
    /**
     * @var string
     */
    protected $sectionName;

    /**
     * @var \Symfony\Component\HttpFoundation\Session\SessionInterface
     */
    protected $session;

    /**
     * @param \Symfony\Component\HttpFoundation\Session\SessionInterface $session
     * @param string $sectionName
     */
    public function __construct(SessionInterface $session = null, $sectionName = 'session')
    {
        $this->session = $session;
        $this->sectionName = $sectionName;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Context $context)
    {
        if ($this->session) {
            $context->setVar($this->sectionName, $this->session->all());
        }

        $this->handleNextNode($context);
    }
}