<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 9/22/18
 * Time: 12:41 AM
 */


namespace PapaLocal\ReferralAgreement\Message\Query\Agreement;


use PapaLocal\Core\ValueObject\GuidInterface;


/**
 * Class FindByGuid
 *
 * @package PapaLocal\ReferralAgreement\Message\Query\Agreement
 */
class FindByGuid
{
    /**
     * @var GuidInterface
     */
    private $agreementGuid;

    /**
     * FindByGuid constructor.
     *
     * @param GuidInterface $guid
     */
    public function __construct(GuidInterface $guid)
    {
        $this->agreementGuid = $guid;
    }

    /**
     * @return GuidInterface
     */
    public function getAgreementGuid(): GuidInterface
    {
        return $this->agreementGuid;
    }
}