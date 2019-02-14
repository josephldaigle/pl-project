<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/22/18
 * Time: 11:19 AM
 */

namespace PapaLocal\IdentityAccess\Message\Command\User;


use PapaLocal\IdentityAccess\Form\CreateUserAccountForm;
use Symfony\Component\Serializer\SerializerInterface;


/**
 * Class CreateUserAccount
 *
 * @package PapaLocal\IdentityAccess\Message\Command\User
 */
class CreateUserAccount
{
    /**
     * @var CreateUserAccountForm
     */
    private $form;

    /**
     * CreateUserAccount constructor.
     *
     * @param CreateUserAccountForm $form
     */
    public function __construct(CreateUserAccountForm $form)
    {
        $this->form = $form;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->form->getUsername();
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->form->getFirstName();
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->form->getLastName();
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
    public function getPassword(): string
    {
        return $this->form->getPassword();
    }

    /**
     * @return string
     */
    public function getConfirmPassword(): string
    {
        return $this->form->getPassword();
    }

    /**
     * @return string
     */
    public function getCompanyName(): string
    {
        return ($this->form->hasCompany()) ? $this->form->getCompanyName() : '';
    }

    /**
     * @return string
     */
    public function getCompanyEmailAddress(): string
    {
        return ($this->form->hasCompany()) ? $this->form->getCompanyEmailAddress() : '';
    }

    /**
     * @return string
     */
    public function getCompanyPhoneNumber(): string
    {
        return ($this->form->hasCompany()) ? $this->form->getCompanyPhoneNumber() : '';
    }

    /**
     * @return array
     */
    public function getCompanyAddress(): array
    {
        return ($this->form->hasCompany()) ? $this->form->getCompanyAddress() : [];
    }
}