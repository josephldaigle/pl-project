<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 9/12/18
 */

namespace PapaLocal\Feed\Enum;


use PapaLocal\Core\Enum\AbstractEnum;


/**
 * Class FeedItemType.
 *
 * @package PapaLocal\Feed\Enum
 */
class FeedItemType extends AbstractEnum
{
    private const REFERRAL_AGREEMENT = 'agreement';
    private const REFERRAL = 'referral';
    private const TRANSACTION = 'transaction';
    private const NOTIFICATION = 'notification';
}