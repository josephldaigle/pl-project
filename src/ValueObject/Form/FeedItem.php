<?php
/**
 * Created by eWebify, LLC.
 * Author: Joe Daigle
 * Date: 5/26/18
 * Time: 10:53 PM
 */


namespace PapaLocal\ValueObject\Form;


/**
 * Class FeedItem
 *
 * Model a feed item selected from the card list.
 *
 * @package PapaLocal\ValueObject\Form
 */
class FeedItem
{
	/**
	 * @var string
	 */
	private $id;

	/**
	 * @var string
	 */
	private $type;

    /**
     * FeedItem constructor.
     *
     * @param string $id
     * @param string $type
     */
    public function __construct(string $id, string $type)
    {
        $this->id   = $id;
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }
}