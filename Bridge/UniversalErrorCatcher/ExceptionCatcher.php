<?php
namespace Fp\BadaBoomBundle\Bridge\UniversalErrorCatcher;

use Symfony\Component\ClassLoader\DebugUniversalClassLoader;

use Fp\BadaBoomBundle\ExceptionCatcher\ExceptionCatcherInterface;
use BadaBoom\ChainNode\ChainNodeInterface;
use BadaBoom\DataHolder\DataHolder;

/**
 * @author Kotlyar Maksim <kotlyar.maksim@gmail.com>
 * @since 9/27/12
 */
class ExceptionCatcher extends \UniversalErrorCatcher_Catcher implements ExceptionCatcherInterface
{
    private $chainNodes = array();
    
    public function registerChainNode(ChainNodeInterface $chainNode)
    {
        if (in_array($chainNode, $this->chainNodes, $strict = true)) {
            return;
        }
        
        $this->chainNodes[] = $chainNode;
        
        $this->registerCallback(function(\Exception $e) use ($chainNode){
            $chainNode->handle($e, new DataHolder());
        });
    }
    
    public function start($debug = false)
    {
        if ($this->isStarted) {
            return;
        }
        
        $this->setThrowSuppressedErrors(false);
        if ($debug) {
            ini_set('display_errors', 1);
            error_reporting(-1);
            DebugUniversalClassLoader::enable();
            $this->setThrowRecoverableErrors(true);
        } else {
            ini_set('display_errors', 0);
            $this->setThrowRecoverableErrors(false);
        }

        parent::start();
    }
}