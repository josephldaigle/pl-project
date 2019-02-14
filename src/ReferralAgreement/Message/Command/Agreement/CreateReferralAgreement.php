<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 9/20/18
 * Time: 8:57 PM
 */


namespace PapaLocal\ReferralAgreement\Message\Command\Agreement;


use PapaLocal\Core\ValueObject\GuidInterface;
use PapaLocal\ReferralAgreement\Form\CreateAgreementForm;


/**
 * Class CreateReferralAgreement
 *
 * @package PapaLocal\ReferralAgreement\Message\Command\Agreement
 */
class CreateReferralAgreement
{
    /**
     * @var GuidInterface
     */
    private $agreementGuid;

    /**
     * @var CreateAgreementForm
     */
    private $createAgreementForm;

    /**
     * @var GuidInterface
     */
    private $userId;

    /**
     * @var GuidInterface
     */
    private $companyGuid;

    /**
     * CreateReferralAgreement constructor.
     *
     * @param GuidInterface       $agreementGuid
     * @param CreateAgreementForm $createAgreementForm
     * @param GuidInterface       $userId
     * @param GuidInterface       $companyGuid
     */
    public function __construct(GuidInterface $agreementGuid, CreateAgreementForm $createAgreementForm, GuidInterface $userId, GuidInterface $companyGuid)
    {
        $this->agreementGuid = $agreementGuid;
        $this->createAgreementForm = $createAgreementForm;
        $this->userId = $userId;
        $this->companyGuid = $companyGuid;
    }

    /**
     * @return GuidInterface
     */
    public function getAgreementGuid(): GuidInterface
    {
        return $this->agreementGuid;
    }

    /**
     * @return CreateAgreementForm
     */
    public function getCreateAgreementForm(): CreateAgreementForm
    {
        return $this->createAgreementForm;
    }

    /**
     * @return GuidInterface
     */
    public function getUserId(): GuidInterface
    {
        return $this->userId;
    }

    /**
     * @return GuidInterface
     */
    public function getCompanyGuid(): GuidInterface
    {
        return $this->companyGuid;
    }
}