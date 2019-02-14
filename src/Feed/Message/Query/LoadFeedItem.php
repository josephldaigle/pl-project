<?php
/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 12/4/18
 * Time: 6:20 PM
 */

namespace PapaLocal\Feed\Message\Query;


/**
 * Class LoadFeedItem
 * @package PapaLocal\Feed\Message\Query
 */
class LoadFeedItem
{
    /**
     * @var string
     */
    private $guid;

    /**
     * @var string
     */
    private $type;

    /**
     * LoadFeedItem constructor.
     * @param string $guid
     * @param string $type
     */
    public function __construct(string $guid, string $type)
    {
        $this->guid = $guid;
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getGuid(): string
    {
        return $this->guid;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }


}