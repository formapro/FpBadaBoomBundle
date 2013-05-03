<?php

namespace Fp\BadaBoomBundle\JsLogger;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class TwigExtension extends \Twig_Extension
{
    private $router;

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    public function getFunctions()
    {
        return array(
            'fp_badaboom_js_error_logger' => new \Twig_Function_Method($this, 'initErrorLogger', array('is_safe' => array('html', 'js'))),
        );
    }

    public function initErrorLogger($level = 'error')
    {
        $url = addslashes($this->router->generate('fp_badaboom_js_logger_log'));

        $js = <<<JS
(function () {
    var oldErrorHandler = window.onerror;
    window.onerror = function(errorMsg, file, line) {
        var enc = encodeURIComponent;
        if (oldErrorHandler) {
            oldErrorHandler(errorMsg, file, line);
        }
        (new Image()).src = '$url?msg=' + enc(errorMsg) +
            '&level=$level' +
            '&context[file]=' + enc(file) +
            '&context[line]=' + enc(line) +
            '&context[browser]=' + enc(navigator.userAgent) +
            '&context[page]=' + enc(document.location.href);
    };
})();
JS;
        $js = preg_replace('{\n *}', '', $js);
        $js = "<script>$js</script>";

        return $js;
    }

    public function getName()
    {
        return 'fb_badaboom_js_logger';
    }
}