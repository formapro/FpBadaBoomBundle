<?php
namespace Fp\BadaBoomBundle\JsLogger;

class JavascriptException extends \Exception
{
    public function __construct($message = '', $file  = '', $line = 0, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->file = $file;
        $this->line = $line;
    }
}