<?php
/**
 * Defines a field in a data type comprising multiple simple types
 *
 * PHP version 5.4
 *
 * @category   LibDNS
 * @package    DataTypes
 * @author     Chris Wright <https://github.com/DaveRandom>
 * @copyright  Copyright (c) Chris Wright <https://github.com/DaveRandom>
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version    2.0.0
 */
namespace LibDNS\DataTypes;

/**
 * Defines a field in a data type comprising multiple simple types
 *
 * @category   LibDNS
 * @package    DataTypes
 * @author     Chris Wright <https://github.com/DaveRandom>
 */
class FieldDefinition
{
    /**
     * @var int
     */
    private $index;

    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $type;

    /**
     * @var bool
     */
    private $allowsMultiple;

    /**
     * @var int
     */
    private $minimumValues;

    /**
     * Constructor
     *
     * @param int    $index
     * @param string $name
     * @param int    $type
     * @param bool   $allowsMultiple
     * @param int    $minimumValues
     */
    public function __construct($index, $name, $type, $allowsMultiple, $minimumValues)
    {
        $this->index = (int) $index;
        $this->name = (string) $name;
        $this->type = (int) $type;
        $this->allowsMultiple = (bool) $allowsMultiple;
        $this->minimumValues = (int) $minimumValues;
    }

    /**
     * Get the index of the field in the data type
     *
     * @return int
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * Get the name of the field
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the type of the field
     *
     * @return int
     */
    public function getType()
    {
        return $this->index;
    }

    /**
     * Determine whether the field allows multiple values
     *
     * @return bool
     */
    public function allowsMultiple()
    {
        return $this->allowsMultiple;
    }

    /**
     * Get the minimum number of values for the field
     *
     * @return int
     */
    public function getMinimumValues()
    {
        return $this->minimumValues;
    }
}
