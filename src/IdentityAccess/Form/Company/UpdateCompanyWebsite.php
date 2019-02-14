<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 12/26/18
 * Time: 8:11 PM
 */

namespace PapaLocal\IdentityAccess\Form\Company;


use Symfony\Component\Validator\Constraints as Assert;


/**
 * Class UpdateCompanyWebsite
 *
 * @package PapaLocal\IdentityAccess\Form\Company
 */
class UpdateCompanyWebsite
{
    /**
     * @var string
     */
    private $guid;

    /**
     * @var string
     *
     * @Assert\NotBlank(
     *     message="Website cannot be blank.",
     *     groups={"save_website"}
     * )
     *
     * @Assert\Length(
     *      max=200,
     *      maxMessage="Website cannot contain more than 200 characters.",
     *      groups={"save_website"}
     * )
     *
     * Only accept  urls formatted as http://www or https://www.
     * Also covers minimum required format (http://a.nm), or 15 chars long.
     * @Assert\Regex(
     *     pattern="/(www)/",
     *     message="The website provided is not in an acceptable format. Please include the www prefix.",
     *     groups={"save_website"}
     * )
     *
     * @Assert\Regex(
     *     pattern="/\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/",
     *     message="The website provided is not in an acceptable format. Please include a suffix(.com, .org, .biz).",
     *     groups={"save_website"}
     * )
     *
     * @Assert\Regex(
     *     pattern="/^(http:\/\/|https:\/\/)(w{3}(\.){1}){0,1}[a-z0-9]+([\-\.]{1}[a-z0-9]+)*(\.{0,1})([a-z]{2,5}(:[0-9]{1,5})?(\/.*)?)?$/",
     *     message="The website provided is not in an acceptable format.",
     *     groups={"save_website"}
     * )
     */
    private $website;

    /**
     * UpdateCompanyWebsite constructor.
     *
     * @param string $guid
     * @param string $website
     */
    public function __construct(string $guid, string $website)
    {
        $this->guid    = $guid;
        $this->website = $website;
    }

    /**
     * @return string
     */
    public function getCompanyGuid(): string
    {
        return $this->guid;
    }

    /**
     * @return string
     */
    public function getWebsite(): string
    {
        return $this->website;
    }

}