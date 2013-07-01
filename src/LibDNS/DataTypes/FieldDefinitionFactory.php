<?php
/**
 * Creates FieldDefinition objects
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
 * Creates FieldDefinition objects
 *
 * @category   LibDNS
 * @package    DataTypes
 * @author     Chris Wright <https://github.com/DaveRandom>
 */
class FieldDefinitionFactory
{
    /**
     * Create a new FieldDefinition object
     *
     * @param int    $index
     * @param string $name
     * @param int    $type
     * @param bool   $allowsMultiple
     * @param int    $minimumValues
     *
     * @return \LibDNS\DataTypes\FieldDefinition
     */
    public function create($index, $name, $type, $allowsMultiple, $minimumValues)
    {
        return new FieldDefinition($index, $name, $type, $allowsMultiple, $minimumValues);
    }
}
