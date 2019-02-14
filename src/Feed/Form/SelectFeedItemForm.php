<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 9/12/18
 */


namespace PapaLocal\Feed\Form;


use PapaLocal\Feed\Enum\FeedItemType;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Class SelectFeedItemForm.
 *
 * @package PapaLocal\Feed\Form
 */
class SelectFeedItemForm
{
    /**
     * @var string
     *
     * @Assert\NotBlank(
     *     message = "The id must be provided."
     * )
     * @Assert\Type(
     *     type = "string",
     *     message = "The id must be a string."
     * )
     * @Assert\Length(
     *     min = 36,
     *     max = 36,
     *     exactMessage = "The id must be exactly {{ limit }} characters."
     * )
     */
    private $guid;

    /**
     * @var FeedItemType
     *
     * @Assert\NotBlank(
     *     message = "The type must be provided."
     * )
     *
     */
    private $type;

    /**
     * SelectFeedItemForm constructor.
     * @param string $guid
     * @param string $type
     */
    public function __construct(string $guid, string $type)
    {
        $this->guid = $guid;
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getGuid()
    {
        return $this->guid;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

}