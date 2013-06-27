<?php
/**
 * Factory which creates ComplexTypeDefinition objects
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
 * Factory which creates ComplexTypeDefinition objects
 *
 * @category   LibDNS
 * @package    DataTypes
 * @author     Chris Wright <https://github.com/DaveRandom>
 */
class ComplexTypeDefinitionFactory
{
    /**
     * Build a new ComplexTypeDefinition object
     *
     * @param array $typeDef
     *
     * @return ComplexTypeDefinition
     */
    public function create(array $typeDef)
    {
        return new ComplexTypeDefinition($typeDef);
    }
}
