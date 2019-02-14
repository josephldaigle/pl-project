<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/22/18
 * Time: 4:09 PM
 */

namespace PapaLocal\Core\Data\Query;


/**
 * Class FindByRowId
 *
 * @package PapaLocal\Core\Data\Query
 */
class FindByRowId
{
    /**
     * @var string
     */
    private $tableName;

    /**
     * @var int
     */
    private $rowId;

    /**
     * FindByRowId constructor.
     *
     * @param string $tableName
     * @param int    $getRowId
     */
    public function __construct($tableName, $getRowId)
    {
        $this->tableName = $tableName;
        $this->rowId     = $getRowId;
    }

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * @return int
     */
    public function getRowId(): int
    {
        return $this->rowId;
    }
}