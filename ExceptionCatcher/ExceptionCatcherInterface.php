<?php
namespace Fp\BadaBoomBundle\ExceptionCatcher;

use BadaBoom\ChainNode\ChainNodeInterface;

/**
 * @author Kotlyar Maksim <kotlyar.maksim@gmail.com>
 * @since 9/27/12
 */
interface ExceptionCatcherInterface
{
    /**
     * @param \BadaBoom\ChainNode\ChainNodeInterface $chainNode
     * 
     * @return void
     */
    function registerChainNode(ChainNodeInterface $chainNode);
    
    /**
     * @param \Exception $e
     * 
     * @return void
     */
    function handleException(\Exception $e);

    /**
     * @return void
     */
    function start($debug = false);
}