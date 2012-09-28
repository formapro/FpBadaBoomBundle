<?php
namespace Fp\BadaBoomBundle;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Kotlyar Maksim <kotlyar.maksim@gmail.com>
 * @since 9/28/12
 */
class BadaBoomFactory
{
    protected $bundles;
    
    public function __construct(array $bundles)
    {
        $this->bundles = $bundles;
    }

    /**
     * @return \Fp\BadaBoomBundle\ExceptionCatcher\ExceptionCatcherInterface
     */
    public function createExceptionCatcher()
    {
        return $this->bundles['FpBadaBoomBundle']->getExceptionCatcher();
    }

    /**
     * @return \Fp\BadaBoomBundle\ChainNode\ChainNodeManagerInterface
     */
    public function createChainNodeManager()
    {
        return $this->bundles['FpBadaBoomBundle']->getChainNodeManager();
    }
}