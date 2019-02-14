<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 12/4/18
 * Time: 10:23 PM
 */

namespace PapaLocal\ReferralAgreement\Message\Query\Agreement;

use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\Feed\Message\Query\LoadFeedItem;
use PapaLocal\ReferralAgreement\Data\InviteeRepository;
use PapaLocal\ReferralAgreement\Data\ReferralAgreementRepository;
use PapaLocal\ReferralAgreement\ValueObject\Strategy;


/**
 * Class LoadFeedItemHandler
 *
 * @package PapaLocal\ReferralAgreement\Message\Query\Agreement
 */
class LoadFeedItemHandler
{
    /**
     * @var ReferralAgreementRepository
     */
    private $agreementRepository;

    /**
     * @var InviteeRepository
     */
    private $inviteeRepository;

    /**
     * LoadFeedItemHandler constructor.
     *
     * @param ReferralAgreementRepository $agreementRepository
     * @param InviteeRepository           $inviteeRepository
     */
    public function __construct(ReferralAgreementRepository $agreementRepository, InviteeRepository $inviteeRepository)
    {
        $this->agreementRepository = $agreementRepository;
        $this->inviteeRepository   = $inviteeRepository;
    }

    /**
     * @inheritDoc
     */
    public function __invoke(LoadFeedItem $query)
    {
        if ('agreement' !== $query->getType()) {
            return [];
        }

        $agreement = $this->agreementRepository->findByGuid(new Guid($query->getGuid()));

        $invitees = $this->inviteeRepository->findAllByAgreementGuid($agreement->getGuid());
        $agreement->setInvitees($invitees);

        $referralCount = $this->agreementRepository->getCurrentPeriodReferralCount($agreement->getGuid());
        $agreement->setReferralCount($referralCount);

        return $agreement;
    }


}