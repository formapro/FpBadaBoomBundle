<?php
namespace Fp\BadaBoomBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

use Fp\BadaBoomBundle\DependencyInjection\Compiler\AddSerializerNormalizersPass;
use Fp\BadaBoomBundle\DependencyInjection\Compiler\AddSerializerEncodersPass;
use Fp\BadaBoomBundle\ExceptionCatcher\ExceptionCatcherInterface;
use Fp\BadaBoomBundle\ChainNode\ChainNodeManagerInterface;

class FpBadaBoomBundle extends Bundle
{
    /**
     * @var ExceptionCatcher\ExceptionCatcherInterface
     */
    protected $exceptionCatcher;

    /**
     * @var ChainNode\ChainNodeManagerInterface
     */
    protected $chainNodeManager;
    
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $badaBoomBundleDefinition = new Definition();
        $badaBoomBundleDefinition->setClass(get_class($this));
        $badaBoomBundleDefinition->setSynthetic(true);
        $container->setDefinition('fp_badaboom', $badaBoomBundleDefinition);
        
        if ($this->exceptionCatcher) { 
            $exceptionCatcherDefinition = new Definition();
            $exceptionCatcherDefinition->setClass('Fp\BadaBoomBundle\BadaBoomFactory');
            $exceptionCatcherDefinition->setFactoryService('fp_badaboom');
            $exceptionCatcherDefinition->setFactoryMethod('getExceptionCatcher');
            
            $container->setDefinition('fp_badaboom.exception_catcher', $exceptionCatcherDefinition);
        }

        if ($this->chainNodeManager) {
            $chainNodeManagerDefinition = new Definition();
            $chainNodeManagerDefinition->setClass('Fp\BadaBoomBundle\BadaBoomFactory');
            $chainNodeManagerDefinition->setFactoryService('fp_badaboom');
            $chainNodeManagerDefinition->setFactoryMethod('getChainNodeManager');

            $container->setDefinition('fp_badaboom.chain_node_manager', $chainNodeManagerDefinition);
        }

        $container->addCompilerPass(new AddSerializerNormalizersPass);
        $container->addCompilerPass(new AddSerializerEncodersPass);
    }

    /**
     * @return ExceptionCatcher\ExceptionCatcherInterface
     */
    public function getExceptionCatcher()
    {
        return $this->exceptionCatcher;
    }

    /**
     * @return ChainNode\ChainNodeManagerInterface
     */
    public function getChainNodeManager()
    {
        return $this->chainNodeManager;
    }
    
    public function boot()
    {
        $this->container->set('fp_badaboom', $this);
        
        /** @var $exceptionCatcher ExceptionCatcherInterface */
        $exceptionCatcher = $this->container->get('fp_badaboom.exception_catcher');
        /** @var $chainNodeManager ChainNodeManagerInterface */
        $chainNodeManager = $this->container->get('fp_badaboom.chain_node_manager');

        $exceptionCatcher->start($this->container->getParameter('kernel.debug'));
        
        foreach ($chainNodeManager->all() as $chainNode) {
            $exceptionCatcher->registerChainNode($chainNode);
        }
    }

    /**
     * @param \Fp\BadaBoomBundle\ExceptionCatcher\ExceptionCatcherInterface $exceptionCatcher
     */
    public function setExceptionCatcher(ExceptionCatcherInterface $exceptionCatcher)
    {
        $this->exceptionCatcher = $exceptionCatcher;
    }

    /**
     * @param \Fp\BadaBoomBundle\ChainNode\ChainNodeManagerInterface $chainNodeManager
     */
    public function setChainNodeManager(ChainNodeManagerInterface $chainNodeManager)
    {
        $this->chainNodeManager = $chainNodeManager;
    }
}