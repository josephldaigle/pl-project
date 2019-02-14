<?php
/**
 * Created by Joseph Daigle.
 * Date: 2/3/19
 * Time: 5:26 PM
 */


namespace PapaLocal\Referral\Message\Query;

use PapaLocal\Core\ValueObject\GuidInterface;


/**
 * FindByAgreementGuid.
 *
 * @package PapaLocal\Referral\Message\Query
 */
class FindByAgreementGuid
{
    /**
     * @var GuidInterface
     */
    private $guid;

    /**
     * FindByAgreementGuid constructor.
     *
     * @param GuidInterface $guid
     */
    public function __construct(GuidInterface $guid)
    {
        $this->guid = $guid;
    }

    /**
     * @return GuidInterface
     */
    public function getAgreementGuid(): GuidInterface
    {
        return $this->guid;
    }
}