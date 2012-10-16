<?php
namespace Fp\BadaBoomBundle\ExceptionCatcher;

use BadaBoom\ChainNode\ChainNodeInterface;
use BadaBoom\Context;

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
            $chainNode->handle(new Context($e));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function start($debug = false)
    {
        //Basic implementation does not set any handlers. 
        //It uses symfony's exception event to handle exception.
    }
}