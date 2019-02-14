<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/20/18
 * Time: 10:02 PM
 */

namespace PapaLocal\ReferralAgreement\Message\Query\Invitee;


use PapaLocal\Core\ValueObject\EmailAddress;


/**
 * Class FindByEmailAddress
 *
 * @package PapaLocal\ReferralAgreement\Message\Query\Invitee
 */
class FindByEmailAddress
{
    /**
     * @var EmailAddress
     */
    private $emailAddress;

    /**
     * FindByEmailAddress constructor.
     *
     * @param EmailAddress $emailAddress
     */
    public function __construct(EmailAddress $emailAddress)
    {
        $this->emailAddress = $emailAddress;
    }

    /**
     * @return EmailAddress
     */
    public function getEmailAddress(): EmailAddress
    {
        return $this->emailAddress;
    }

}