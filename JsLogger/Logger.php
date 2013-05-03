<?php

namespace Fp\BadaBoomBundle\JsLogger;

use Psr\Log\LoggerInterface;

class Logger
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function write($level, $message, array $context = array())
    {
        if (!$message) {
            return false;
        }

        $level = strtolower($level);
        $this->logger->log($level, $message, $context);

        return true;
    }
}