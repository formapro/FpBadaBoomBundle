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
    public function shouldGetDataHolderNormalizer()
    {
        $dataHolderNormalizerPlainText = self::$container->get('fp.badaboom.normalizer.data_holder');

        $this->assertInstanceOf(
            'BadaBoom\Serializer\Normalizer\DataHolderNormalizer',
            $dataHolderNormalizerPlainText
        );
    }

    /**
     * @test
     */
    public function shouldGetTextEncoder()
    {
        $dataHolderNormalizerPlainText = self::$container->get('fp.badaboom.encoder.text');

        $this->assertInstanceOf(
            'BadaBoom\Serializer\Encoder\TextEncoder',
            $dataHolderNormalizerPlainText
        );
    }

    /**
     * @test
     */
    public function shouldGetSerializer()
    {
        $serializer = self::$container->get('fp.badaboom.serializer');

        $this->assertInstanceOf('Symfony\Component\Serializer\Serializer', $serializer);
    }

    /**
     * @test
     */
    public function shouldGetSerializerWhichSupportsNormalizationOfDataHolder()
    {
        $serializer = self::$container->get('fp.badaboom.serializer');

        $this->assertTrue($serializer->supportsNormalization($this->createDataHolderMock()));
    }

    /**
     * @test
     */
    public function shouldGetSerializerWhichSupportsEncodingToPlainTextFormat()
    {
        $serializer = self::$container->get('fp.badaboom.serializer');

        $this->assertTrue($serializer->supportsEncoding('plain-text'));
    }

    protected function createDataHolderMock()
    {
        return $this->getMock('BadaBoom\DataHolder\DataHolderInterface');
    }
}