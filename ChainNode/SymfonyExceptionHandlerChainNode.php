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
     * @var boolean
     */
    protected $debug;

    /**
     * @param boolean $debug
     */
    public function __construct($debug)
    {
        $this->debug = $debug;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(\Exception $exception, DataHolderInterface $data)
    {
        if ('cli' === PHP_SAPI) {
            $this->handleNextNode($exception, $data);
            
            return;
        }
            
        $symfonyExceptionHandler = new ExceptionHandler($this->debug);
        $symfonyExceptionHandler->handle($exception);

        $this->handleNextNode($exception, $data);
    }
}