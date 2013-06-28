<?php
/**
 * Object which holds data about how the RDATA sections of known resource record types are structured
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
 * Object which holds data about how the RDATA sections of known resource record types are structured
 *
 * @category   LibDNS
 * @package    DataTypes
 * @author     Chris Wright <https://github.com/DaveRandom>
 */
class DataTypeDefinitions
{
    /**
     * @var array[] How the RDATA sections of known resource record types are structured
     */
    private $typeDefs = [
    ];

    /**
     * Get a complex type definition for a record type if it is known
     *
     * @param int $recordType The record type, can be indicated using the RecordTypes enum
     *
     * @return int|int[]|null
     */
    public function getTypeDefinition($recordType)
    {
        return isset($typeDefs[$recordType]) ? $typeDefs[$recordType] : null;
    }
}
