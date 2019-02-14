<?php
/**
 * Created by PhpStorm.
 * User: Joe Daigle
 * Date: 12/3/18
 * Time: 9:21 PM
 */


namespace PapaLocal\ReferralAgreement\Message\Query\Agreement;


use PapaLocal\Entity\Collection\Collection;
use PapaLocal\Feed\Message\Query\LoadFeed;
use PapaLocal\ReferralAgreement\Data\InviteeRepository;
use PapaLocal\ReferralAgreement\Data\ReferralAgreementRepository;
use PapaLocal\ReferralAgreement\Exception\AgreementNotFoundException;


/**
 * Class LoadFeedItemsHandler
 *
 * @package PapaLocal\ReferralAgreement\Message\Query\Agreement
 */
class LoadFeedHandler
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
     * LoadFeedHandler constructor.
     *
     * @param ReferralAgreementRepository $agreementRepository
     * @param InviteeRepository           $inviteeRepository
     */
    public function __construct(ReferralAgreementRepository $agreementRepository, InviteeRepository $inviteeRepository)
    {
        $this->agreementRepository = $agreementRepository;
        $this->inviteeRepository = $inviteeRepository;
    }

    /**
     * @param LoadFeed $query
     *
     * @return array|Collection
     * @throws AgreementNotFoundException
     */
    public function __invoke(LoadFeed $query)
    {
        // do nothing if agreements not selected
        if (! in_array('agreement', $query->getFeedType()))
        {
            return [];
        }

        // load invitee agreements
        $inviteeAgmts = $this->agreementRepository->loadInviteeAgreements($query->getUser()->getGuid());

        // load participant agreements
        $participantAgmts = $this->agreementRepository->loadParticipantAgreements($query->getUser()->getGuid());
        foreach($participantAgmts as $agreement) {
            $referralCount = $this->agreementRepository->getCurrentPeriodReferralCount($agreement->getGuid());
            $agreement->setReferralCount($referralCount);
        }

        // load user agreements
        $userAgmts = $this->agreementRepository->loadUserAgreements($query->getUser()->getGuid());
        foreach($userAgmts as $agmt) {
            $invitees = $this->inviteeRepository->findAllByAgreementGuid($agmt->getGuid());
            $agmt->setInvitees($invitees);
        }

        $feedList = $inviteeAgmts->addAll($participantAgmts->all())
            ->addAll($userAgmts);

        return $feedList;
    }
}