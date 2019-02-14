<?php
/**
 * Created by PhpStorm.
 * User: Yacouba Keita
 * Date: 10/12/18
 * Time: 1:12 PM
 */


namespace PapaLocal\Referral\Message;


use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\Core\ValueObject\GuidInterface;
use PapaLocal\Referral\Form\DisputeResolution;
use PapaLocal\Referral\Form\ReferralForm;
use PapaLocal\Referral\Form\ReferralRate;
use PapaLocal\Referral\Message\Command\CreateReferral;
use PapaLocal\Referral\Message\Command\RateReferral;
use PapaLocal\Referral\Message\Command\ResolveDispute;
use PapaLocal\Referral\Message\Query\FindByAgreementGuid;
use PapaLocal\Referral\Message\Query\FindByGuid;


/**
 * Class MessageFactory
 * @package PapaLocal\Referral\Message
 */
class MessageFactory
{
    /********************************
     *  COMMAND
     *******************************/

    /**
     * @param ReferralForm $form
     * @param Guid $providerGuid
     * @return CreateReferral
     */
    public function newCreateReferral(ReferralForm $form, Guid $providerGuid)
    {
        return new CreateReferral($form, $providerGuid);
    }

    /**
     * @param ReferralRate $form
     * @return RateReferral
     */
    public function newRateReferral(ReferralRate $form)
    {
        return new RateReferral($form);
    }

    /**
     * @param DisputeResolution $form
     * @param Guid $reviewerGuid
     * @return ResolveDispute
     */
    public function newResolveDispute(DisputeResolution $form, Guid $reviewerGuid)
    {
        return new ResolveDispute($form, $reviewerGuid);
    }

    /********************************
     *  QUERY
     *******************************/

    /**
     * @param GuidInterface $guid
     *
     * @return FindByGuid
     */
    public function newFindByGuid(GuidInterface $guid): FindByGuid
    {
        return new FindByGuid($guid);
    }

    /**
     * @param GuidInterface $agreementGuid
     *
     * @return FindByAgreementGuid
     */
    public function newFindByAgreementGuid(GuidInterface $agreementGuid): FindByAgreementGuid
    {
        return new FindByAgreementGuid($agreementGuid);
    }
}