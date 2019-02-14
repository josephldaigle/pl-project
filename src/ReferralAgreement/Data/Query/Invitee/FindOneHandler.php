<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 10/15/18
 */

namespace PapaLocal\ReferralAgreement\Data\Query\Invitee;


use PapaLocal\Core\Data\TableGatewayInterface;
use PapaLocal\ReferralAgreement\Data\Mapper\Invitee\InviteeMapper;
use PapaLocal\ReferralAgreement\Entity\ReferralAgreementInvitee;
use Symfony\Component\Serializer\SerializerInterface;


/**
 * Class FindOneHandler.
 *
 * @package PapaLocal\ReferralAgreement\Data\Query\Invitee
 */
class FindOneHandler
{
    /**
     * @var TableGatewayInterface
     */
    private $tableGateway;

    /**
     * @var InviteeMapper
     */
    private $inviteeMapper;

    /**
     * FindOneHandler constructor.
     *
     * @param TableGatewayInterface $tableGateway
     * @param InviteeMapper         $inviteeMapper
     */
    public function __construct(TableGatewayInterface $tableGateway, InviteeMapper $inviteeMapper)
    {
        $this->tableGateway  = $tableGateway;
        $this->inviteeMapper = $inviteeMapper;
    }


    /**
     * @param FindOne $query
     *
     * @return null|ReferralAgreementInvitee
     */
    public function __invoke(FindOne $query)
    {
        $this->tableGateway->setTable('v_referral_agreement_invitee');
        $records = $this->tableGateway->findByColumns(array(
            'agreementGuid' => $query->getAgreementGuid(),
            'emailAddress' => $query->getInviteeEmailAddress(),
        ));

        //
        if ($records->count() > 1) {
            trigger_error(sprintf('More than one invitation exists for %s on agreement %s. Potential data corruption.', $query->getInviteeEmailAddress(), $query->getAgreementGuid()), E_USER_WARNING);
        }

        if ($records->count() < 1) {
            return null;
        }

        return $this->inviteeMapper->mapToObject($records->current());
    }
}