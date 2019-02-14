<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 10/24/18
 */


namespace Test\Integration\Core\ValueObject\Serialization;


use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\Entity\Collection\Collection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Serializer\SerializerInterface;


/**
 * Class SerializerTest.
 *
 * @package Test\Integration\Core\ValueObject\Serialization
 */
class SerializerTest extends KernelTestCase
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        // boot kernel
        self::bootKernel();

        // fetch services
        $this->serializer = static::$container->get('serializer');

    }

    public function testCanFetchServices()
    {
        $this->assertInstanceOf(SerializerInterface::class, $this->serializer);
    }

    public function testCanDenormalizeCollection()
    {
        // set up fixtures
        $data = [
            (object) ['col1' => 'val1', 'col2', 'val2']
        ];

        $collection = $this->serializer->denormalize(array('items' => $data), Collection::class, 'array');

        $this->assertCount(count($data), $collection, 'unexpected count');
    }
}