<?php
namespace Fp\BadaBoomBundle\ChainNode;

use Symfony\Component\HttpKernel\Debug\ExceptionHandler;

use BadaBoom\ChainNode\AbstractChainNode;
use BadaBoom\DataHolder\DataHolderInterface;

/**
 * @author Kotlyar Maksim <kotlyar.maksim@gmail.com>
 * @since 4/10/12
 */
class SymfonyExceptionHandlerChainNode extends AbstractChainNode
{
    /**
     * @var \Symfony\Component\HttpKernel\Debug\ExceptionHandler
     */
    protected $exceptionHandler;

    /**
     * @param \Symfony\Component\HttpKernel\Debug\ExceptionHandler $exceptionHandler
     */
    public function __construct(ExceptionHandler $exceptionHandler)
    {
        $this->exceptionHandler = $exceptionHandler;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(\Exception $exception, DataHolderInterface $data)
    {
        $this->exceptionHandler->handle($exception);

        $this->handleNextNode($exception, $data);
    }
}