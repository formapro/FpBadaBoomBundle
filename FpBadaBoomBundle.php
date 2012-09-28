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
     * @param ExceptionCatcher\ExceptionCatcherInterface $exceptionCatcher
     * @param ChainNode\ChainNodeManagerInterface $chainNodeManager
     */
    public function __construct(ExceptionCatcherInterface $exceptionCatcher = null, ChainNodeManagerInterface $chainNodeManager = null)
    {
        $this->exceptionCatcher = $exceptionCatcher;
        $this->chainNodeManager = $chainNodeManager;
    }
    
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        
        if ($this->exceptionCatcher) { 
            $exceptionCatcherDefinition = new Definition();
            $exceptionCatcherDefinition->setClass('Fp\BadaBoomBundle\BadaBoomFactory');
            $exceptionCatcherDefinition->setFactoryService('fp_badaboom.factory');
            $exceptionCatcherDefinition->setFactoryMethod('createExceptionCatcher');
            
            $container->setDefinition('fp_badaboom.exception_catcher', $exceptionCatcherDefinition);
        }

        if ($this->chainNodeManager) {
            $chainNodeManagerDefinition = new Definition();
            $chainNodeManagerDefinition->setClass('Fp\BadaBoomBundle\BadaBoomFactory');
            $chainNodeManagerDefinition->setFactoryService('fp_badaboom.factory');
            $chainNodeManagerDefinition->setFactoryMethod('createChainNodeManager');

            $container->setDefinition('fp_badaboom.chain_node_manager', $chainNodeManagerDefinition);
        }

        $container->addCompilerPass(new AddSerializerNormalizersPass);
        $container->addCompilerPass(new AddSerializerEncodersPass);
    }
    
    public function boot()
    {
        /** @var $exceptionCatcher ExceptionCatcherInterface */
        $exceptionCatcher = $this->container->get('fp_badaboom.exception_catcher');
        /** @var $chainNodeManager ChainNodeManagerInterface */
        $chainNodeManager = $this->container->get('fp_badaboom.chain_node_manager');

        $exceptionCatcher->start();
        
        foreach ($chainNodeManager->all() as $chainNode) {
            $exceptionCatcher->registerChainNode($chainNode);
        }
    }
}