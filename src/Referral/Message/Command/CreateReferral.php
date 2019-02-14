<?php

/**
 * Created by PhpStorm.
 * Date: 10/12/18
 * Time: 7:52 AM
 */

namespace PapaLocal\Referral\Message\Command;


use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\Referral\Form\ReferralForm;


/**
 * Class CreateReferral
 * @package PapaLocal\Referral\Message\Command
 */
class CreateReferral
{
    /**
     * @var ReferralForm
     */
    private $form;

    /**
    * @var Guid
    */
    private $providerGuid;

    /**
     * CreateReferral constructor.
     * @param ReferralForm $form
     * @param Guid $providerGuid
     */
    public function __construct(ReferralForm $form, Guid $providerGuid)
    {
        $this->form = $form;
        $this->providerGuid = $providerGuid;
    }

    /**
     * @return ReferralForm
     */
    public function getForm(): ReferralForm
    {
        return $this->form;
    }

    /**
     * @return Guid
     */
    public function getProviderGuid(): Guid
    {
        return $this->providerGuid;
    }

}