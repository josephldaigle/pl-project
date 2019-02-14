<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/3/18
 * Time: 10:35 PM
 */

namespace Test\Integration\Core\Data\Query;


use PapaLocal\Core\Data\Query\QueryHandlerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


/**
 * Class QueryTest
 *
 * @package Test\Integration\Core\Data\Query
 */
class QueryAvailabilityTest extends KernelTestCase
{
    protected function setUp()
    {
        parent::setUp();

        // boot kernel
        self::bootKernel();
    }

    /**
     * @return array
     */
    public function handlerServiceIdProvider()
    {
        return [
            ['PapaLocal\Core\Data\Query\FindByGuidHandler'],
            ['PapaLocal\Core\Data\Query\FindByHandler'],
            ['PapaLocal\Core\Data\Query\FindByColsHandler'],
            ['PapaLocal\Core\Data\Query\FindByRowIdHandler']
        ];
    }

    /**
     * Tests that all query handlers are available in the service container.
     *
     * @dataProvider handlerServiceIdProvider
     */
    public function testHandlerAvailabilityAsService(string $serviceId)
    {
        try {
            $handler = static::$container->get($serviceId);

            $this->assertTrue(is_object($handler), 'unexpected type');

        } catch (\Exception $exception) {
            $this->fail($exception->getMessage());
        }

    }
}