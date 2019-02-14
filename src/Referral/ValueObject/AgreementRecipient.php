<?php
/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 9/11/18
 * Time: 1:50 PM
 */

namespace PapaLocal\Referral\ValueObject;


use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\Referral\Entity\RecipientInterface;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Class AgreementRecipient
 * @package PapaLocal\Referral\Form
 */
class AgreementRecipient implements RecipientInterface
{
    /**
     * @var Guid
     *
     * @Assert\NotBlank(
     *     message = "Please choose an agreement, or select contact as recipient of this referral.",
     *     groups = {"agreement"}
     * )
     *
     * @Assert\Valid()
     */
    private $guid;

    /**
     * AgreementRecipient constructor.
     * @param Guid|null $guid
     */
    public function __construct(Guid $guid = null)
    {
        $this->guid = $guid;
    }

    /**
     * @return mixed
     */
    public function getGuid()
    {
        return $this->guid;
    }
}