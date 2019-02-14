<?php
/**
 * Created by eWebify, LLC.
 * Author: Joe Daigle
 * Date: 6/6/18
 * Time: 1:32 PM
 */

namespace PapaLocal\Test;


use PapaLocal\Entity\FeedItemInterface;


/**
 * Class FeedItemDummy
 *
 * @package PapaLocal\Test
 *
 * FeedItemInterface test dummy.
 */
class FeedItemDummy implements FeedItemInterface
{
	public function getId(): int
	{
		// TODO: Implement getId() method.
	}

    public function getGuid()
    {
        // TODO: Implement getGuid() method.
    }

    public function getTitle(): string
	{
		// TODO: Implement getTitle() method.
	}

	public function getTimeCreated(): string
	{
		// TODO: Implement getTimeCreated() method.
	}

	public function getTimeUpdated(): string
	{
		// TODO: Implement getTimeUpdated() method.
	}

	public function getFeedType(): string
	{
		// TODO: Implement getFeedType() method.
	}

	public function getCardBody(): string
	{
		// TODO: Implement getCardBody() method.
	}
}