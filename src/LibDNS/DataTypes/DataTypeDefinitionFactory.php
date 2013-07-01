<?php
/**
 * Creates DataTypeDefinition objects
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
 * Creates DataTypeDefinition objects
 *
 * @category   LibDNS
 * @package    DataTypes
 * @author     Chris Wright <https://github.com/DaveRandom>
 */
class DataTypeDefinitionFactory
{
    /**
     * Create a new DataTypeDefinition object
     *
     * @param FieldDefinitionFactory $fieldDefFactory
     * @param int[]                  $typeDef         Structural definition of the fields
     *
     * @return \LibDNS\DataTypes\DataTypeDefinition
     *
     * @throws \InvalidArgumentException When the type definition is invalid
     */
    public function create(FieldDefinitionFactory $fieldDefFactory, array $typeDef)
    {
        return new DataTypeDefinition($fieldDefFactory, $typeDef);
    }
}
