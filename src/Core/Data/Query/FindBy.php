<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 10/23/18
 */

namespace PapaLocal\Core\Data\Query;

/**
 * Class FindBy.
 *
 * @package PapaLocal\Core\Data\Query
 */
class FindBy
{
    /**
     * @var string
     */
    private $tableName;

    /**
     * @var string
     */
    private $columnName;

    /**
     * @var string
     */
    private $value;

    /**
     * FindBy constructor.
     *
     * @param string $tableName
     * @param string $columnName
     * @param string $value
     */
    public function __construct(string $tableName, string $columnName, string $value)
    {
        $this->tableName = $tableName;
        $this->columnName = $columnName;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * @return string
     */
    public function getColumnName(): string
    {
        return $this->columnName;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }
}