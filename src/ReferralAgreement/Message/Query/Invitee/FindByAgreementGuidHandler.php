<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/20/18
 * Time: 10:34 PM
 */

namespace PapaLocal\ReferralAgreement\Message\Query\Invitee;


use PapaLocal\ReferralAgreement\Data\InviteeRepository;


/**
 * Class FindByAgreementGuidHandler
 *
 * @package PapaLocal\ReferralAgreement\Message\Query\Invitee
 */
class FindByAgreementGuidHandler
{
    /**
     * @var InviteeRepository
     */
    private $inviteeRepository;

    /**
     * FindByAgreementGuidHandler constructor.
     *
     * @param InviteeRepository $inviteeRepository
     */
    public function __construct(InviteeRepository $inviteeRepository)
    {
        $this->inviteeRepository = $inviteeRepository;
    }

    /**
     * @param FindByAgreementGuid $query
     *
     * @return \PapaLocal\Entity\Collection\Collection
     */
    public function __invoke(FindByAgreementGuid $query)
    {
        return $this->inviteeRepository->findAllByAgreementGuid($query->getAgreementGuid());
    }
}