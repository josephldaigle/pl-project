<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 9/11/18
 * Time: 7:56 AM
 */


namespace PapaLocal\ReferralAgreement\Form;


use PapaLocal\Core\ValueObject\GuidInterface;


/**
 * Class PublishReferralAgreementForm
 *
 * Model the form used to publish an agreement.
 *
 * @package PapaLocal\ReferralAgreement\Form
 */
class PublishReferralAgreementForm
{
    /**
     * @var GuidInterface
     */
    private $referralAgreementId;

    /**
     * @var GuidInterface
     */
    private $publisherId;


    /**
     * PublishReferralAgreementForm constructor.
     *
     * @param GuidInterface $referralAgreementId
     * @param GuidInterface $publisherId
     */
    public function __construct(GuidInterface $referralAgreementId, GuidInterface $publisherId)
    {
        $this->referralAgreementId = $referralAgreementId;
        $this->publisherId = $publisherId;
    }

    /**
     * @return string
     */
    public function getReferralAgreementId(): string
    {
        return $this->referralAgreementId->value();
    }

    /**
     * @return string
     */
    public function getPublisherId(): string
    {
        return $this->publisherId->value();
    }
}