<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 12/16/18
 * Time: 9:16 PM
 */

namespace PapaLocal\IdentityAccess\Message\Command\Company;


use PapaLocal\Core\ValueObject\GuidInterface;
use PapaLocal\IdentityAccess\Form\Company\CreateCompany as CreateCompanyForm;


/**
 * Class CreateCompany
 *
 * @package PapaLocal\IdentityAccess\Message\Command\Company
 */
class CreateCompany
{
    /**
     * @var GuidInterface
     */
    private $ownerUserGuid;

    /**
     * @var CreateCompanyForm
     */
    private $form;

    /**
     * CreateCompany constructor.
     *
     * @param GuidInterface     $ownerUserGuid
     * @param CreateCompanyForm $form
     */
    public function __construct(GuidInterface $ownerUserGuid, CreateCompanyForm $form)
    {
        $this->ownerUserGuid = $ownerUserGuid;
        $this->form          = $form;
    }

    /**
     * @return GuidInterface
     */
    public function getOwnerUserGuid(): GuidInterface
    {
        return $this->ownerUserGuid;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->form->getName();
    }

    /**
     * @return string
     */
    public function getPhoneNumber(): string
    {
        return $this->form->getPhoneNumber();
    }

    /**
     * @return string
     */
    public function getEmailAddress(): string
    {
        return $this->form->getEmailAddress();
    }

    /**
     * @return array
     */
    public function getAddress(): array
    {
        return $this->form->getAddress();
    }
}