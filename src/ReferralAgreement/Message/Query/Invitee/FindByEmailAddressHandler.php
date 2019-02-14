<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/20/18
 * Time: 10:14 PM
 */

namespace PapaLocal\ReferralAgreement\Message\Query\Invitee;


use PapaLocal\ReferralAgreement\Data\InviteeRepository;


/**
 * Class FindByEmailAddressHandler
 *
 * @package PapaLocal\ReferralAgreement\Message\Query\Invitee
 */
class FindByEmailAddressHandler
{
    /**
     * @var InviteeRepository
     */
    private $inviteeRepository;

    /**
     * FindByEmailAddressHandler constructor.
     *
     * @param InviteeRepository $inviteeRepository
     */
    public function __construct(InviteeRepository $inviteeRepository)
    {
        $this->inviteeRepository = $inviteeRepository;
    }

    public function __invoke(FindByEmailAddress $query)
    {
        return $this->inviteeRepository->findAllByEmailAddress($query->getEmailAddress());
    }

}