<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 9/22/18
 * Time: 12:53 AM
 */

namespace PapaLocal\ReferralAgreement\Message\Command\Agreement;


use PapaLocal\Core\ValueObject\GuidInterface;


/**
 * Class UpdateName.
 *
 * @package PapaLocal\ReferralAgreement\Message\Command\Agreement
 */
class UpdateName
{
    /**
     * @var GuidInterface
     */
    private $agreementGuid;

    /**
     * @var string
     */
    private $name;

    /**
     * UpdateName constructor.
     *
     * @param GuidInterface $agreementGuid
     * @param string        $name
     */
    public function __construct(GuidInterface $agreementGuid, string $name)
    {
        $this->agreementGuid = $agreementGuid;
        $this->name = $name;
    }

    /**
     * @return GuidInterface
     */
    public function getAgreementGuid(): GuidInterface
    {
        return $this->agreementGuid;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

}