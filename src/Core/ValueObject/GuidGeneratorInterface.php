<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 9/26/18
 * Time: 1:32 PM
 */


namespace PapaLocal\Core\ValueObject;


/**
 * Interface GuidGeneratorInterface
 *
 * Describe a Guid generator.
 *
 * @package PapaLocal\Core\ValueObject
 */
interface GuidGeneratorInterface
{
    /**
     * @return GuidInterface
     */
    public function generate(): GuidInterface;

    /**
     * @param string $guid
     *
     * @return GuidInterface
     */
    public function createFromString(string $guid): GuidInterface;
}