<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 9/24/18
 * Time: 7:43 PM
 */


namespace PapaLocal\ReferralAgreement\Data\Command\Agreement;


use PapaLocal\Core\ValueObject\GuidInterface;


/**
 * Class UpdateAgreementName.
 *
 * @package PapaLocal\ReferralAgreement\Data\Command\Agreement
 */
class UpdateAgreementName
{
    /**
     * @var GuidInterface
     */
    private $agreementGuid;

    /**
     * @var string
     */
    private $newName;

    /**
     * UpdateAgreementName constructor.
     *
     * @param GuidInterface $agreementGuid
     * @param string        $newName
     */
    public function __construct(GuidInterface $agreementGuid, string $newName)
    {
        $this->agreementGuid = $agreementGuid;
        $this->newName = $newName;
    }


    /**
     * @return string
     */
    public function getAgreementGuid(): string
    {
        return $this->agreementGuid->value();
    }

    /**
     * @return string
     */
    public function getNewName(): string
    {
        return $this->newName;
    }
}