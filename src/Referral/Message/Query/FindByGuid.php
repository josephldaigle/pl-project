<?php
/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 1/21/19
 * Time: 5:18 PM
 */

namespace PapaLocal\Referral\Message\Query;


use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\Core\ValueObject\GuidInterface;


/**
 * Class FindByGuid
 * @package PapaLocal\Referral\Message\Query
 */
class FindByGuid
{
    /**
     * @var Guid
     */
    private $guid;

    /**
     * FindByGuid constructor.
     * @param GuidInterface $guid
     */
    public function __construct(GuidInterface $guid)
    {
        $this->guid = $guid;
    }

    /**
     * @return Guid
     */
    public function getGuid(): Guid
    {
        return $this->guid;
    }
}