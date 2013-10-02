<?php
namespace Fp\BadaBoomBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class TestController extends Controller
{
    public function exceptionAction()
    {
        throw new \Exception('test exception');
    }

    public function logExceptionAction()
    {
        $this->get('logger.exception')->error(new \Exception('test exception'));

        return new Response('OK');
    }

    public function errorRecoverableAction()
    {
        trigger_error('test notice', \E_USER_NOTICE);

        return new Response('OK');
    }

    public function errorFatalAction()
    {
        $obj = new \stdClass();
        $obj->foo();
    }

    public function errorJsAction()
    {
        return $this->render('FpBadaBoomBundle:Test:errorJs.html.twig');
    }
} 