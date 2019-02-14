<?php
/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 10/4/18
 * Time: 2:40 PM
 */

namespace PapaLocal\Referral\Event;


use PapaLocal\Core\ValueObject\GuidInterface;
use Symfony\Component\EventDispatcher\Event;


/**
 * Class ReferralDisputedEvent
 * @package PapaLocal\Referral\Event
 */
class ReferralDisputedEvent extends Event
{
    /**
     * @var GuidInterface
     */
    private $referralGuid;

    /**
     * @var GuidInterface
     */
    private $agreementGuid;

    /**
     * ReferralDisputedEvent constructor.
     * @param GuidInterface $referralGuid
     * @param GuidInterface $agreementGuid
     */
    public function __construct(GuidInterface $referralGuid, GuidInterface $agreementGuid)
    {
        $this->referralGuid = $referralGuid;
        $this->agreementGuid = $agreementGuid;
    }

    /**
     * @return GuidInterface
     */
    public function getReferralGuid(): GuidInterface
    {
        return $this->referralGuid;
    }

    /**
     * @return GuidInterface
     */
    public function getAgreementGuid(): GuidInterface
    {
        return $this->agreementGuid;
    }
}