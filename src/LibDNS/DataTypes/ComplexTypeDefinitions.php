<?php
/**
 * Object which holds data about how known complex types are structured
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
 * Object which holds data about how known complex types are structured
 *
 * @category   LibDNS
 * @package    DataTypes
 * @author     Chris Wright <https://github.com/DaveRandom>
 */
class ComplexTypeDefinitions
{
    /**
     * @var array[] Data about how known complex types are structured
     */
    private $typeDefs = [
    ];

    /**
     * @var ComplexTypeDefinition[] Cache of previously created objects
     */
    private $cache = [];

    /**
     * @var ComplexTypeDefinitionFactory Factory which creates ComplexTypeDefinition objects
     */
    private $complexTypeDefinitionFactory;

    /**
     * Constructor
     *
     * @param ComplexTypeDefinitionFactory $complexTypeDefinitionFactory Factory which creates ComplexTypeDefinition objects
     */
    public function __construct(ComplexTypeDefinitionFactory $complexTypeDefinitionFactory)
    {
        $this->complexTypeDefinitionFactory = $complexTypeDefinitionFactory;
    }

    /**
     * Get a complex type definition for a record type if it is known
     *
     * @param int $recordType The record type, can be indicated using the RecordTypes enum
     *
     * @return ComplexTypeDefinition|null
     */
    public function getTypeDefinition($recordType)
    {
        if (!array_key_exists($recordType, $cache)) {
            $cache[$recordType] = isset($typeDefs[$recordType]) ? $this->complexTypeDefinitionFactory->create($typeDefs[$recordType]) : null;
        }

        return $cache[$recordType];
    }
}
