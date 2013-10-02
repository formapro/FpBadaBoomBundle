<?php
namespace Fp\BadaBoomBundle\ChainNode;

use Symfony\Component\HttpKernel\Debug\ExceptionHandler;

use BadaBoom\ChainNode\AbstractChainNode;
use BadaBoom\Context;

/**
 * @author Kotlyar Maksim <kotlyar.maksim@gmail.com>
 * @since 4/10/12
 */
class SymfonyExceptionHandlerChainNode extends AbstractChainNode
{
    /**
     * @var boolean
     */
    protected $debug;

    /**
     * @var boolean
     */
    protected $isEnabled = true;

    /**
     * @param boolean $debug
     */
    public function __construct($debug)
    {
        $this->debug = $debug;
    }

    /**
     * This allows to disable handler on boot.
     * If not it will show gray exception message near the cool one of symfony exception controller.
     *
     * @var boolean $boolean
     */
    public function setEnabled($boolean)
    {
        $this->isEnabled = !!$boolean;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(Context $context)
    {
        if (false == $this->isEnabled) {
            $this->handleNextNode($context);

            return;
        }
        if ('cli' === php_sapi_name()) {
            $this->handleNextNode($context);
            
            return;
        }
            
        $symfonyExceptionHandler = new ExceptionHandler($this->debug);
        $symfonyExceptionHandler->handle($context->getException());

        $this->handleNextNode($context);
    }
}