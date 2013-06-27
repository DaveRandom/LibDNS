<?php
/**
 * Factory which creates ComplexType objects
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
 * Factory which creates ComplexType objects
 *
 * @category   LibDNS
 * @package    DataTypes
 * @author     Chris Wright <https://github.com/DaveRandom>
 */
class ComplexTypeFactory
{
    /**
     * Create a new ComplexType object
     *
     * @return ComplexType
     */
    public function create(ComplexTypeDefinition $typeDef = null)
    {
        return new ComplexType($typeDef);
    }
}
