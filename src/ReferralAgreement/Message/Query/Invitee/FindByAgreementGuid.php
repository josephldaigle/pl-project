<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/20/18
 * Time: 10:34 PM
 */

namespace PapaLocal\ReferralAgreement\Message\Query\Invitee;


use PapaLocal\Core\ValueObject\GuidInterface;


/**
 * Class FindByAgreementGuid
 *
 * @package PapaLocal\ReferralAgreement\Message\Query\Invitee
 */
class FindByAgreementGuid
{
    /**
     * @var GuidInterface
     */
    private $agreementGuid;

    /**
     * FindByAgreementGuid constructor.
     *
     * @param GuidInterface $agreementGuid
     */
    public function __construct(GuidInterface $agreementGuid)
    {
        $this->agreementGuid = $agreementGuid;
    }

    /**
     * @return GuidInterface
     */
    public function getAgreementGuid(): GuidInterface
    {
        return $this->agreementGuid;
    }

}