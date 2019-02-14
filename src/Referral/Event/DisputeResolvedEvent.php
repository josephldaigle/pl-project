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
 * Class DisputeResolvedEvent
 * @package PapaLocal\Referral\Event
 */
class DisputeResolvedEvent extends Event
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
     * @var string
     */
    private $status;

    /**
     * DisputeResolvedEvent constructor.
     * @param GuidInterface $referralGuid
     * @param GuidInterface $agreementGuid
     * @param string $status
     */
    public function __construct(GuidInterface $referralGuid, GuidInterface $agreementGuid, $status)
    {
        $this->referralGuid = $referralGuid;
        $this->agreementGuid = $agreementGuid;
        $this->status = $status;
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

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }
}