# FpBadaBoomBundle [![Build Status](https://secure.travis-ci.org/formapro/FpBadaBoomBundle.png?branch=master)](http://travis-ci.org/formapro/FpBadaBoomBundle)

An example of AppKernel

```php
<?php

use BadaBoom\Adapter\Cache\ArrayCacheAdapter;
use BadaBoom\Adapter\Mailer\NativeMailerAdapter;
use BadaBoom\Adapter\Logger\NativeLoggerAdapter;
use BadaBoom\ChainNode\Sender\NewrelicSender;
use BadaBoom\ChainNode\Filter\DuplicateExceptionFilter;
use BadaBoom\ChainNode\Sender\SentrySender;
use BadaBoom\ChainNode\Provider\ExceptionSummaryProvider;
use BadaBoom\ChainNode\Provider\ExceptionSubjectProvider;
use BadaBoom\ChainNode\Provider\ServerProvider;
use BadaBoom\ChainNode\Provider\SessionProvider;
use BadaBoom\ChainNode\Provider\EnvironmentProvider;
use BadaBoom\ChainNode\Provider\ExceptionStackTraceProvider;
use BadaBoom\ChainNode\Sender\MailSender;
use BadaBoom\ChainNode\Sender\LogSender;
use BadaBoom\DataHolder\DataHolder;
use BadaBoom\Serializer\Encoder\TextEncoder;
use BadaBoom\Serializer\Encoder\LineEncoder;
use BadaBoom\Serializer\Normalizer\RecursionSafeContextNormalizer;
use Fp\BadaBoomBundle\Bridge\UniversalErrorCatcher\ExceptionCatcher;
use Fp\BadaBoomBundle\ChainNode\SafeChainNodeManager;
use Fp\BadaBoomBundle\Bridge\UniversalErrorCatcher\ChainNode\SymfonyExceptionHandlerChainNode;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Serializer\Serializer;

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
     * @var \Fp\BadaBoomBundle\Bridge\UniversalErrorCatcher\ChainNode\SymfonyExceptionHandlerChainNode
     */
    protected $symfonyExceptionHandlerChainNode;

    public function registerBundles()
    {
        $bundles = array(
            new Fp\BadaBoomBundle\FpBadaBoomBundle($this->exceptionCatcher, $this->chainNodeManager),

            //...
        );

        return $bundles;
    }

    /**
     * {@inheritDoc}
     */
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

    protected function initializeChainNodeManager()
    {
        if (extension_loaded('newrelic')) {
            $this->chainNodeManager->addSender('default', new NewrelicSender());
        }

        $this->symfonyExceptionHandlerChainNode = new SymfonyExceptionHandlerChainNode($this->isDebug());
        $this->chainNodeManager->addSender('default', $this->symfonyExceptionHandlerChainNode);

        $this->chainNodeManager->addFilter('default', new DuplicateExceptionFilter(new ArrayCacheAdapter));

        // prod env
        if (false == $this->isDebug()) {
            $recipients = array(
                'dev@example.com',
            );

            $this->chainNodeManager->addProvider('default', new ExceptionSubjectProvider());
            $this->chainNodeManager->addProvider('default', new ExceptionSummaryProvider());
            $this->chainNodeManager->addProvider('default', new ExceptionStackTraceProvider());
            $this->chainNodeManager->addProvider('default', new ServerProvider());
            $this->chainNodeManager->addProvider('default', new SessionProvider());
            $this->chainNodeManager->addProvider('default', new EnvironmentProvider());

            $serializer = new Serializer(
                [new RecursionSafeContextNormalizer],
                [new TextEncoder, new LineEncoder]
            );

            touch($logFile = $this->getRootDir().'/logs/'.$this->getEnvironment().'-exceptions.log');
            $this->chainNodeManager->addSender('default', new LogSender(
                new NativeLoggerAdapter($logFile),
                $serializer,
                new DataHolder(array(
                    'format' => 'line'
                ))
            ));

            $domain = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'example.com';
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
            if (isset($_SERVER['SENTRY_DSN'])) {
                $this->chainNodeManager->addSender(
                    'default',
                    new SentrySender(new \Raven_Client($_SERVER['SENTRY_DSN']))
                );
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function boot()
    {
        parent::boot();

        $this->symfonyExceptionHandlerChainNode->setEnabled(false);
    }
}
```
