<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/17/18
 * Time: 8:02 AM
 */

namespace PapaLocal\ReferralAgreement\Data\Query\Invitee;


use PapaLocal\Core\Data\TableGatewayInterface;
use PapaLocal\Entity\Collection\Collection;
use PapaLocal\ReferralAgreement\Data\Mapper\Invitee\InviteeMapper;


/**
 * Class FindAllByHandler
 *
 * @package PapaLocal\ReferralAgreement\Data\Query\Invitee
 */
class FindAllByHandler
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
     * FindAllByHandler constructor.
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
     * @param FindAllBy $query
     *
     * @return null|Collection
     */
    function __invoke(FindAllBy $query)
    {
        $this->tableGateway->setTable('v_referral_agreement_invitee');
        $records = $this->tableGateway->findByColumns($query->getFilterVars());

        if ($records->count() < 1) {
            return null;
        }

        return $this->inviteeMapper->mapToList($records);
    }

}