<?php
/**
 * Creates DataTypeDefinitionManager objects
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
 * Creates DataTypeDefinitionManager objects
 *
 * @category   LibDNS
 * @package    DataTypes
 * @author     Chris Wright <https://github.com/DaveRandom>
 */
class DataTypeDefinitionManagerFactory
{
    /**
     * Create a new DataTypeDefinitionManager object
     *
     * @return \LibDNS\DataTypes\DataTypeDefinitionManager
     */
    public function create()
    {
        return new DataTypeDefinitionManager(new DataTypeDefinitionFactory, new FieldDefinitionFactory);
    }
}
