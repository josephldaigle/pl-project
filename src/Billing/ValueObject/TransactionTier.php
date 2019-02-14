<?php
/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 1/21/19
 * Time: 12:43 PM
 */

namespace PapaLocal\Billing\ValueObject;


use PapaLocal\Core\Enum\AbstractEnum;


/**
 * Class TransactionTier
 * @package PapaLocal\Billing\ValueObject
 */
class TransactionTier extends AbstractEnum
{
    public const TIER_ONE_USER = 0.6;
}