<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 9/27/18
 * Time: 10:36 AM
 */

namespace PapaLocal\ReferralAgreement\ValueObject;


use PapaLocal\Core\Enum\AbstractEnum;


/**
 * Class StatusChangeReason
 *
 * @package PapaLocal\ReferralAgreement\ValueObject
 */
class StatusChangeReason extends AbstractEnum
{
    private const CREATED = 'Created';
    private const PUBLISHED = 'Published';
    private const ACTIVATED = 'Activated';  // unpaused
    private const REFERRAL_QUOTA_REACHED = 'Quota Reached';
    private const OWNER_REQUESTED = 'Owner Requested';
    private const INSUFFICIENT_FUNDS = 'Insufficient Funding';
}