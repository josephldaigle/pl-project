<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 12/16/18
 * Time: 8:40 PM
 */

namespace PapaLocal\IdentityAccess\Form\Company;


use Symfony\Component\Validator\Constraints as Assert;


/**
 * Class UpdateName
 *
 * @package PapaLocal\IdentityAccess\Form\Company
 */
class UpdateName
{
    /**
     * @var string
     */
    private $guid;

    /**
     * @var string
     *
     * @Assert\NotBlank(
     *     message = "Company name must be present."
     * )
     */
    private $name;

    /**
     * UpdateName constructor.
     *
     * @param string $guid
     * @param string $name
     */
    public function __construct($guid, $name = null)
    {
        $this->guid = $guid;
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getGuid(): string
    {
        return $this->guid;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }
}