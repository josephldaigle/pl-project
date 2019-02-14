<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 9/20/18
 */


namespace PapaLocal\Core\ValueObject;


/**
 * Interface GuidInterface.
 *
 * Describe a UUID/GUID.
 *
 * @package PapaLocal\Core\ValueObject
 */
interface GuidInterface
{
    /**
     * @return string
     */
    public function value(): string;
}