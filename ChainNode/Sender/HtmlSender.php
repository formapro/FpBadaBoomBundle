<?php
namespace Fp\BadaBoomBundle\ChainNode\Sender;

use Symfony\Component\HttpKernel\Debug\ExceptionHandler;

use BadaBoom\ChainNode\AbstractChainNode;
use BadaBoom\DataHolder\DataHolderInterface;

class HtmlSender extends AbstractChainNode
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
     * @param \Exception $exception
     * @param \BadaBoom\DataHolder\DataHolderInterface $data
     */
    public function handle(\Exception $exception, DataHolderInterface $data)
    {
        $this->exceptionHandler->handle($exception);

        $this->handleNextNode($exception, $data);
    }
}