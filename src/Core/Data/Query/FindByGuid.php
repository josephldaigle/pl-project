<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/22/18
 * Time: 3:56 PM
 */

namespace PapaLocal\Core\Data\Query;


use PapaLocal\Core\ValueObject\GuidInterface;


/**
 * Class FindByGuid
 *
 * A query to find a row from a table by it's guid.
 *
 * @package PapaLocal\Core\Data\Query
 */
class FindByGuid
{
    /**
     * @var string
     */
    private $tableName;

    /**
     * @var GuidInterface
     */
    private $guid;

    /**
     * FindByGuid constructor.
     *
     * @param string        $tableName
     * @param GuidInterface $guid
     */
    public function __construct(string  $tableName, GuidInterface $guid)
    {
        $this->tableName = $tableName;
        $this->guid      = $guid;
    }

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * @return GuidInterface
     */
    public function getGuid(): string
    {
        return $this->guid->value();
    }
}