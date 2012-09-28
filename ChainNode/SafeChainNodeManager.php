<?php
namespace Fp\BadaBoomBundle\ChainNode;

use BadaBoom\ChainNode\ChainNodeInterface;
use BadaBoom\ChainNode\ChainNodeCollection;
use BadaBoom\ChainNode\Decorator\SafeChainNodeDecorator;

/**
 * @author Kotlyar Maksim <kotlyar.maksim@gmail.com>
 * @since 9/27/12
 */
class SafeChainNodeManager extends ChainNodeManager 
{
    /**
     * {@inheritdoc}
     */
    public function addProvider($name, ChainNodeInterface $provider)
    {
        parent::addProvider($name, new SafeChainNodeDecorator($provider));
    }

    /**
     * {@inheritdoc}
     */
    public function addSender($name, ChainNodeInterface $sender)
    {
        parent::addSender($name, new SafeChainNodeDecorator($sender));
    }

    /**
     * {@inheritdoc}
     */
    public function addFilter($name, ChainNodeInterface $filter)
    {
        parent::addFilter($name, new SafeChainNodeDecorator($filter));
    }
}