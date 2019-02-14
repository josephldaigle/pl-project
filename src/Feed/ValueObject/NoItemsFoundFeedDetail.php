<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 12/11/18
 * Time: 9:08 PM
 */

namespace PapaLocal\Feed\ValueObject;


use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\Entity\FeedItemInterface;


/**
 * Class NoItemsFoundFeedDetail
 *
 * @package PapaLocal\Feed\ValueObject
 */
class NoItemsFoundFeedDetail implements FeedItemInterface
{
    public const GUID = 'f7245f1c-e024-42e5-9409-9351b578046a';

    /**
     * @inheritDoc
     */
    public function getGuid()
    {
        return new Guid(self::GUID);
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return 'No Items Found';
    }

    /**
     * @inheritDoc
     */
    public function getTimeCreated(): string
    {
        return date('Y-m-d H:i:s', time());
    }

    /**
     * @inheritDoc
     */
    public function getTimeUpdated(): string
    {
        return $this->getTimeCreated();
    }

    /**
     * @inheritDoc
     */
    public function getFeedType(): string
    {
        return 'not found';
    }

    /**
     * @inheritDoc
     */
    public function getCardBody(): string
    {
        return 'We weren\'t able to find any items matching your criteria.';
    }

}