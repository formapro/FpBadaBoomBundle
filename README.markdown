# FpBadaBoomBundle [![Build Status](https://secure.travis-ci.org/formapro/FpBadaBoomBundle.png?branch=master)](http://travis-ci.org/formapro/FpBadaBoomBundle)

An example of AppKernel

```php
<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Serializer\Serializer;

use BadaBoom\ChainNode\Provider\ExceptionSummaryProvider;
use BadaBoom\ChainNode\Provider\ExceptionSubjectProvider;
use BadaBoom\ChainNode\Provider\ServerProvider;
use BadaBoom\ChainNode\Provider\SessionProvider;
use BadaBoom\ChainNode\Provider\EnvironmentProvider;
use BadaBoom\ChainNode\Provider\ExceptionStackTraceProvider;
use BadaBoom\Adapter\Mailer\NativeMailerAdapter;
use BadaBoom\DataHolder\DataHolder;
use BadaBoom\Serializer\Encoder\TextEncoder;
use BadaBoom\ChainNode\Sender\MailSender;
use BadaBoom\ChainNode\Sender\LogSender;
use BadaBoom\Adapter\Logger\NativeLoggerAdapter;
use BadaBoom\Serializer\Normalizer\ContextNormalizer;

use Fp\BadaBoomBundle\Bridge\UniversalErrorCatcher\ExceptionCatcher;
use Fp\BadaBoomBundle\ChainNode\SafeChainNodeManager;
use Fp\BadaBoomBundle\Bridge\UniversalErrorCatcher\ChainNode\SymfonyExceptionHandlerChainNode;

class AppKernel extends Kernel
{
    /**
     * @var \Fp\BadaBoomBundle\ExceptionCatcher\ExceptionCatcherInterface
     */
    protected $exceptionCatcher;

    /**
     * @var \Fp\BadaBoomBundle\ChainNode\ChainNodeManagerInterface
     */
    protected $chainNodeManager;

    /**
     * @var \Rj\CoreBundle\Badaboom\SymfonyExceptionHandlerChainNode
     */
    protected $symfonyExceptionHandlerChainNode;

    public function registerBundles()
    {
        $bundles = array(
            // ...
            
            new Fp\BadaBoomBundle\FpBadaBoomBundle($this->exceptionCatcher, $this->chainNodeManager),
        );

        return $bundles;
    }

    public function init()
    {
        $this->exceptionCatcher = new ExceptionCatcher;
        $this->chainNodeManager = new SafeChainNodeManager;

        $this->exceptionCatcher->start($this->isDebug());

        $this->initializeChainNodeManager();

        foreach ($this->chainNodeManager->all() as $chainNode) {
            $this->exceptionCatcher->registerChainNode($chainNode);
        }
    }

    public function initializeChainNodeManager()
    {
        $this->symfonyExceptionHandlerChainNode = new SymfonyExceptionHandlerChainNode($this->isDebug());
        $this->chainNodeManager->addSender('default', $this->symfonyExceptionHandlerChainNode);
        
        // prod env
        if (false == $this->isDebug()) {
            $recipients = array('acme@example.com');

            $this->chainNodeManager->addProvider('default', new ExceptionSubjectProvider());
            $this->chainNodeManager->addProvider('default', new ExceptionSummaryProvider());
            $this->chainNodeManager->addProvider('default', new ExceptionStackTraceProvider());
            $this->chainNodeManager->addProvider('default', new ServerProvider());
            $this->chainNodeManager->addProvider('default', new SessionProvider());
            $this->chainNodeManager->addProvider('default', new EnvironmentProvider());

            $serializer = new Serializer(
                array(new ContextNormalizer()),
                array(new TextEncoder())
            );

            touch($logFile = $this->getRootDir().'/logs/'.$this->getEnvironment().'-exceptions.log');
            $this->chainNodeManager->addSender('default', new LogSender(
                new NativeLoggerAdapter($logFile),
                $serializer,
                new DataHolder(array(
                    'format' => 'text'
                ))
            ));

            $domain = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'remixjobs.com';
            $this->chainNodeManager->addSender('default', new MailSender(
                new NativeMailerAdapter,
                $serializer,
                new DataHolder(array(
                    'sender' => 'noreply@'.$domain,
                    'recipients' => $recipients,
                    'subject' => 'Whoops, looks like something went wrong.',
                    'format' => 'text',
                    'headers' => array()
                ))
            ));
        }
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'_local.yml');
    }
}
```
