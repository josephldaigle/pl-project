<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/3/18
 * Time: 10:24 PM
 */

namespace PapaLocal\Core\Data\Query;


/**
 * Class FindByCols
 *
 * @package PapaLocal\Core\Data\Query
 */
class FindByCols
{
    /**
     * @var string
     */
    private $tableName;

    /**
     * @var array an associative array of col => value mappings that will be used as AND WHERE col = value
     */
    private $predicates;

    /**
     * FindByCols constructor.
     *
     * @param string $tableName
     * @param array  $predicates
     */
    public function __construct($tableName, array $predicates)
    {
        $this->tableName  = $tableName;
        $this->predicates = $predicates;
    }

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * @return array
     */
    public function getPredicates(): array
    {
        return $this->predicates;
    }
}