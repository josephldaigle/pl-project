<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/2/18
 * Time: 1:26 PM
 */


namespace PapaLocal\ReferralAgreement\Message\Command\Agreement;


use PapaLocal\Core\ValueObject\GuidInterface;


/**
 * Class PublishAgreement.
 *
 * @package PapaLocal\ReferralAgreement\Message\Command\Agreement
 */
class PublishAgreement
{
    /**
     * @var GuidInterface
     */
    private $agreementGuid;

    /**
     * PublishAgreement constructor.
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