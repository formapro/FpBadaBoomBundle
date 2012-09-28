<?php
namespace Fp\BadaBoomBundle\ChainNode;

use BadaBoom\ChainNode\ChainNodeInterface;
use BadaBoom\ChainNode\ChainNodeCollection;

/**
 * @author Kotlyar Maksim <kotlyar.maksim@gmail.com>
 * @since 9/27/12
 */
class ChainNodeManager implements ChainNodeManagerInterface 
{
    protected $chains = array();
    
    protected $providers = array();
    
    protected $senders = array();
    
    protected $filters = array();
    
    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return $this->chains;
    }

    /**
     * {@inheritdoc}
     */
    public function get($name)
    {
        if (false == isset($this->chains[$name])) {
            throw new \InvalidArgumentException(sprintf(
                'The chain with such name %s was not added.',
                $name
            ));
        }
        
        return $this->chains[$name];
    }
    
    /**
     * {@inheritdoc}
     */
    public function addProvider($name, ChainNodeInterface $provider)
    {
        $this->initializeChain($name);
        
        $this->providers[$name]->append($provider);
    }

    /**
     * {@inheritdoc}
     */
    public function addSender($name, ChainNodeInterface $sender)
    {
        $this->initializeChain($name);

        $this->senders[$name]->append($sender);
    }

    /**
     * {@inheritdoc}
     */
    public function addFilter($name, ChainNodeInterface $filter)
    {
        $this->initializeChain($name);

        $this->filters[$name]->append($filter);
    }
    
    protected function initializeChain($name)
    {
        if (false == isset($this->chains[$name])) {
            $this->chains[$name] = $chain = new ChainNodeCollection();
            $this->providers[$name] = $provider = new ChainNodeCollection();
            $this->filters[$name] = $filter = new ChainNodeCollection();
            $this->senders[$name] = $sender = new ChainNodeCollection();

            $chain->append($filter);
            $chain->append($provider);
            $chain->append($sender);
        }
    }
}