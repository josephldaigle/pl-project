<?php
/**
 * Created by PhpStorm.
 * User: Yacouba
 * Date: 11/17/17
 * Time: 11:42 AM
 */

namespace PapaLocal\Entity;


use PapaLocal\Core\ValueObject\GuidInterface;


/**
 * Interface FeedItemInterface
 *
 * @package PapaLocal\Entity
 *
 * Describe an item that can be displayed in the user's feed.
 */
interface FeedItemInterface
{

	public const FEED_TYPE_REFERRAL = 'referral';
	public const FEED_TYPE_AGREEMENT = 'agreement';
	public const FEED_TYPE_NOTIFICATION = 'notification';

    /**
     * @return GuidInterface
     */
    public function getGuid();

	/**
	 * @return string
	 */
	public function getTitle(): string ;

	/**
	 * @return string
	 */
	public function getTimeCreated(): string;

    /**
     * @return mixed the timestamp of the last database update operation
     */
    public function getTimeUpdated(): string;

    /**
     * Fetch the type of feed item this class represents. (Should correspond to a template name in the view)
     * @return mixed
     */
    public function getFeedType(): string;

	/**
	 * Provide a string representation of the body for the Feed card.
	 *
	 * @return string
	 */
	public function getCardBody(): string;
}