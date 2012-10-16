<?php
namespace Fp\BadaBoomBundle\Bridge\UniversalErrorCatcher\ChainNode;

use Fp\BadaBoomBundle\ChainNode\SymfonyExceptionHandlerChainNode as BaseSymfonyExceptionHandlerChainNode;
use BadaBoom\Context;

/**
 * @author Kotlyar Maksim <kotlyar.maksim@gmail.com>
 * @since 4/10/12
 */
class SymfonyExceptionHandlerChainNode extends BaseSymfonyExceptionHandlerChainNode
{
    /**
     * {@inheritdoc}
     */
    public function handle(Context $context)
    {
        //filter recoverable errors.
        $exception = $context->getException();
        if ($exception instanceof \ErrorException && false == $exception instanceof \FatalErrorException) {
            $this->handleNextNode($context);
            
            return;
        }
            
        parent::handle($context);
    }
}