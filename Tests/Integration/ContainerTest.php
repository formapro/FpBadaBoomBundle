<?php
namespace Fp\BadaBoomBundle\Tests\Integration;

class ContainerTest extends \PHPUnit_Framework_TestCase
{
    public static $container;

    public static function setUpBeforeClass()
    {
        require_once __DIR__ . '/app/AppKernel.php';

        $kernel = new \AppKernel();
        $kernel->boot();

        self::$container = $kernel->getContainer();
    }

    /**
     * @test
     */
    public function shouldGetContextNormalizer()
    {
        $contextNormalizer = self::$container->get('fp_badaboom.normalizer.context');

        $this->assertInstanceOf('BadaBoom\Serializer\Normalizer\ContextNormalizer', $contextNormalizer);
    }

    /**
     * @test
     */
    public function shouldGetTextEncoder()
    {
        $textEncoder = self::$container->get('fp_badaboom.encoder.text');

        $this->assertInstanceOf('BadaBoom\Serializer\Encoder\TextEncoder', $textEncoder);
    }

    /**
     * @test
     */
    public function shouldGetSerializer()
    {
        $serializer = self::$container->get('fp_badaboom.serializer');

        $this->assertInstanceOf('Symfony\Component\Serializer\Serializer', $serializer);
    }

    /**
     * @test
     */
    public function shouldGetSerializerWhichSupportsNormalizationOfContext()
    {
        $serializer = self::$container->get('fp_badaboom.serializer');

        $this->assertTrue($serializer->supportsNormalization($this->createContextMock()));
    }

    /**
     * @test
     */
    public function shouldGetSerializerWhichSupportsEncodingToPlainTextFormat()
    {
        $serializer = self::$container->get('fp_badaboom.serializer');

        $this->assertTrue($serializer->supportsEncoding('plain-text'));
    }

    protected function createContextMock()
    {
        return $this->getMock('BadaBoom\Context', array(), array(new \Exception));
    }
}