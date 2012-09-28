<?php
namespace Fp\BadaBoomBundle\ExceptionCatcher;

use BadaBoom\ChainNode\ChainNodeInterface;
use BadaBoom\DataHolder\DataHolder;

/**
 * @author Kotlyar Maksim <kotlyar.maksim@gmail.com>
 * @since 9/27/12
 */
class ExceptionCatcher implements ExceptionCatcherInterface
{
    protected $chainNodes = array();
    
    /**
     * {@inheritdoc}
     */
    public function registerChainNode(ChainNodeInterface $chainNode)
    {
        if (in_array($chainNode, $this->chainNodes, $strict = true)) {
            return;
        }
        
        $this->chainNodes[] = $chainNode;
    }

    /**
     * {@inheritdoc}
     */
    public function handleException(\Exception $e)
    {
        foreach ($this->chainNodes as $chainNode) {
            $chainNode->handle($e, new DataHolder());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function start($debug = false)
    {
        set_exception_handler(array($this, 'handleException'));
    }
}