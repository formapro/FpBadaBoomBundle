<?php

namespace Fp\BadaBoomBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Fp\BadaBoomBundle\JsLogger\Logger;

class LogController extends Controller
{
    public function createAction(Request $request)
    {
        $level = $request->query->get('level');
        $message = $request->query->get('msg');
        $context = $request->query->get('context', array());

        $logger = $this->getLogger();

        if ($logger->write($level, $message, $context)) {
            return new Response(base64_decode('R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs'), 201, array('Content-Type' => 'image/gif'));
        }

        return new Response('', 400);
    }

    /**
     * @return Logger
     */
    protected function getLogger()
    {
        return $this->get('fp_badaboom_js_logger.logger');
    }
}