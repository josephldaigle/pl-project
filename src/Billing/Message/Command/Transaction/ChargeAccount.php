<?php
/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 1/21/19
 * Time: 10:29 AM
 */

namespace PapaLocal\Billing\Message\Command\Transaction;


use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\Core\ValueObject\GuidInterface;


/**
 * Class ChargeAccount
 * @package PapaLocal\Billing\Message\Command\Transaction
 */
class ChargeAccount
{
    /**
     * @var Guid
     */
    private $agreementGuid;

    /**
     * @var Guid
     */
    private $referralGuid;

    /**
     * ChargeAccount constructor.
     *
     * @param GuidInterface $agreementGuid
     * @param GuidInterface $referralGuid
     */
    public function __construct(GuidInterface $agreementGuid, GuidInterface $referralGuid)
    {
        $this->agreementGuid = $agreementGuid;
        $this->referralGuid = $referralGuid;
    }

    /**
     * @return Guid
     */
    public function getAgreementGuid(): Guid
    {
        return $this->agreementGuid;
    }

    /**
     * @return Guid
     */
    public function getReferralGuid(): Guid
    {
        return $this->referralGuid;
    }
}