<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 12/18/18
 */


namespace PapaLocal\IdentityAccess\ValueObject;


use PapaLocal\Core\ValueObject\Guid;


/**
 * Class SystemAdmin.
 *
 * @package PapaLocal\IdentityAccess\ValueObject
 */
class SystemAdmin
{
    public const GUID = '20671da2-82c6-4b30-8140-b7146cc8033b';

    /**
     * @return Guid
     */
    public static function guid() {
        return new Guid(self::GUID);
    }
}