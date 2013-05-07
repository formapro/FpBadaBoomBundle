<?php
namespace Fp\BadaBoomBundle\Exception;

class JavascriptException extends \Exception
{
    /**
     * @param string $message
     * @param string $file
     * @param int $line
     * @param int $code
     * @param \Exception $previous
     */
    public function __construct($message = '', $file  = '', $line = 0, $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->file = $file;
        $this->line = $line;
    }
}