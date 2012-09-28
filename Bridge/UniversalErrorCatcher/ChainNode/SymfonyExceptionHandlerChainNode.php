<?php
namespace Fp\BadaBoomBundle\Bridge\UniversalErrorCatcher\ChainNode;

use Symfony\Component\HttpKernel\Debug\ExceptionHandler;

use BadaBoom\DataHolder\DataHolderInterface;
use Fp\BadaBoomBundle\ChainNode\SymfonyExceptionHandlerChainNode as BaseSymfonyExceptionHandlerChainNode;

/**
 * @author Kotlyar Maksim <kotlyar.maksim@gmail.com>
 * @since 4/10/12
 */
class SymfonyExceptionHandlerChainNode extends BaseSymfonyExceptionHandlerChainNode
{
    /**
     * {@inheritdoc}
     */
    public function handle(\Exception $exception, DataHolderInterface $data)
    {
        //filter recoverable errors.
        if ($exception instanceof \ErrorException && false == $exception instanceof \FatalErrorException) {
            $this->handleNextNode($exception, $data);
            
            return;
        }
            
        parent::handle($exception, $data);
    }
}