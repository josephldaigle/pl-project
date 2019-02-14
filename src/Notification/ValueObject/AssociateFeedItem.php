<?php
/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 12/13/18
 * Time: 11:36 AM
 */

namespace PapaLocal\Notification\ValueObject;


use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\Feed\Enum\FeedItemType;


/**
 * Class AssociateFeedItem
 * @package PapaLocal\Notification\ValueObject\Strategy
 */
class AssociateFeedItem
{
    /**
     * @var Guid
     */
    private $guid;

    /**
     * @var FeedItemType
     */
    private $type;

    /**
     * AssociateFeedItem constructor.
     * @param Guid $guid
     * @param FeedItemType $type
     */
    public function __construct(Guid $guid, FeedItemType $type)
    {
        $this->guid = $guid;
        $this->type = $type;
    }

    /**
     * @return Guid
     */
    public function getGuid(): Guid
    {
        return $this->guid;
    }

    /**
     * @return FeedItemType
     */
    public function getType(): FeedItemType
    {
        return $this->type;
    }


}