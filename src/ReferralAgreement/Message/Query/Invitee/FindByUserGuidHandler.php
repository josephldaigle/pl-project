<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/20/18
 * Time: 10:24 PM
 */

namespace PapaLocal\ReferralAgreement\Message\Query\Invitee;


use PapaLocal\ReferralAgreement\Data\InviteeRepository;


/**
 * Class FindByUserGuidHandler
 *
 * @package PapaLocal\ReferralAgreement\Message\Query\Invitee
 */
class FindByUserGuidHandler
{
    /**
     * @var InviteeRepository
     */
    private $inviteeRepository;

    /**
     * FindByUserGuidHandler constructor.
     *
     * @param InviteeRepository $inviteeRepository
     */
    public function __construct(InviteeRepository $inviteeRepository)
    {
        $this->inviteeRepository = $inviteeRepository;
    }

    /**
     * @param FindByUserGuid $query
     *
     * @return \PapaLocal\Entity\Collection\Collection
     */
    public function __invoke(FindByUserGuid $query)
    {
        return $this->inviteeRepository->findAllByUserGuid($query->getUserGuid());
    }

}