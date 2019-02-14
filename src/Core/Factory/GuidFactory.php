<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 9/26/18
 * Time: 2:27 PM
 */


namespace PapaLocal\Core\Factory;


use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\Core\ValueObject\GuidInterface;
use Ramsey\Uuid\Uuid;
use PapaLocal\Core\ValueObject\GuidGeneratorInterface;


/**
 * Class GuidFactory
 *
 * A factory for creating Guid objects, globally unique identifers.
 *
 * @package PapaLocal\Core\Factory
 */
class GuidFactory implements GuidGeneratorInterface
{
    /**
     * Return a new Guid object.
     *
     * @return GuidInterface
     */
    public function generate(): GuidInterface
    {
        $guid = new Guid(Uuid::uuid4());
        return $guid;
    }

    /**
     * @param string $guid
     *
     * @return GuidInterface
     * @throws \InvalidArgumentException
     */
    public function createFromString(string $guid): GuidInterface
    {
        if (strlen($guid) !== 36) {
            throw new \InvalidArgumentException(sprintf('Param 1 supplied to %s must be a 36 character long string.', __METHOD__));
        }

        return new Guid($guid);
    }

}