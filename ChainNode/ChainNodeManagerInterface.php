<?php
namespace Fp\BadaBoomBundle\ChainNode;

use BadaBoom\ChainNode\ChainNodeInterface;

/**
 * @author Kotlyar Maksim <kotlyar.maksim@gmail.com>
 * @since 9/27/12
 */
interface ChainNodeManagerInterface
{
    /**
     * @return array of ChainNodeInterface
     */
    function all();

    /**
     * @param string $name
     * 
     * @return ChainNodeInterface
     */
    function get($name);

    /**
     * @param string $name
     * @param \BadaBoom\ChainNode\ChainNodeInterface $provider
     * 
     * @return void
     */
    function addProvider($name, ChainNodeInterface $provider);

    /**
     * @param string $name
     * @param \BadaBoom\ChainNode\ChainNodeInterface $filter
     *
     * @return void
     */
    function addFilter($name, ChainNodeInterface $filter);

    /**
     * @param string $name
     * @param \BadaBoom\ChainNode\ChainNodeInterface $sender
     *
     * @return void
     */
    function addSender($name, ChainNodeInterface $sender);
}
