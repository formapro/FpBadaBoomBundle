<?php
namespace Fp\BadaBoomBundle\Tests\Exception;

use Fp\BadaBoomBundle\Exception\JavascriptException;

class JavascriptExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function couldBeConstructedWithoutArguments()
    {
        new JavascriptException;
    }

    /**
     * @test
     */
    public function shouldBeSubclassOfException()
    {
        $this->assertInstanceOf('Exception', new JavascriptException);
    }

    /**
     * @test
     */
    public function shouldSetCorrectInitializationValues()
    {
        $exception = new JavascriptException('test_message', 'test_file', 10);

        $this->assertEquals('test_file', $exception->getFile());
        $this->assertEquals(10, $exception->getLine());
    }
}
