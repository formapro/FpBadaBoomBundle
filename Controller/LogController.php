<?php

namespace Fp\BadaBoomBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use BadaBoom\Bridge\Psr\Logger;

use Fp\BadaBoomBundle\Exception\JavascriptException;

class LogController extends Controller
{
    public function createAction(Request $request)
    {
        $level = $request->query->get('level');
        $message = $request->query->get('msg');
        $context = $request->query->get('context', array());

        $exception = new JavascriptException($message, $context['file'], $context['line']);

        $this->getLogger()->log($level, $exception, $context);

        return new Response(base64_decode('R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs'), 201, array('Content-Type' => 'image/gif'));
    }

    /**
     * @return Logger
     */
    protected function getLogger()
    {
        return $this->get('fp_badaboom.logger');
    }
}