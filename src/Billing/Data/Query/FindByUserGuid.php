<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 9/23/18
 * Time: 5:02 PM
 */


namespace PapaLocal\Billing\Data\Query;



/**
 * Class FindByUserGuid
 *
 * @package PapaLocal\Billing\Data\Query
 */
class FindByUserGuid
{
    /**
     * @var string
     */
    private $guid;

    /**
     * FindByUserGuid constructor.
     *
     * @param string $userId
     */
    public function __construct(string $userId)
    {
        $this->guid = $userId;
    }

    /**
     * @return string
     */
    public function getGuid(): string
    {
        return $this->guid;
    }
}