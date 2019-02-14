<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/21/18
 * Time: 9:19 PM
 */

namespace PapaLocal\IdentityAccess\ValueObject;


use PapaLocal\Core\Enum\AbstractEnum;


/**
 * Class SecurityRole
 *
 * @package PapaLocal\IdentityAccess\ValueObject
 */
class SecurityRole extends AbstractEnum
{
    private const ROLE_SUPERADMIN = 'ROLE_SUPERADMIN';
    private const ROLE_ADMIN   = 'ROLE_ADMIN';
    private const ROLE_USER   = 'ROLE_USER';
    private const ROLE_COMPANY = 'ROLE_COMPANY';
    private const ROLE_REFERRAL_PARTNER   = 'ROLE_REFERRAL_PARTNER';
}