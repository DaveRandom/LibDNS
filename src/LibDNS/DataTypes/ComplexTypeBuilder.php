<?php
/**
 * Class for objects which build complex types from type definitions
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
 * Class for objects which build complex types from type definitions
 *
 * @category   LibDNS
 * @package    DataTypes
 * @author     Chris Wright <https://github.com/DaveRandom>
 */
class ComplexTypeBuilder
{
    /**
     * @var SimpleTypeFactory Factory which creates SimpleType objects
     */
    private $simpleTypeFactory;

    /**
     * @var ComplexTypeFactory Factory which creates ComplexType objects
     */
    private $complexTypeFactory;

    /**
     * @var ComplexTypeDefinitions Object which holds definition data for known complex types
     */
    private $typeDefs;

    /**
     * Constructor
     *
     * @param SimpleTypeFactory      $simpleTypeFactory  Factory which creates SimpleType objects
     * @param ComplexTypeFactory     $complexTypeFactory Factory which creates ComplexType objects
     * @param ComplexTypeDefinitions $typeDefs           Object which holds definition data for known complex types
     */
    public function __construct(SimpleTypeFactory $simpleTypeFactory, ComplexTypeFactory $complexTypeFactory, ComplexTypeDefinitions $typeDefs)
    {
        $this->simpleTypeFactory = $simpleTypeFactory;
        $this->complexTypeFactory = $complexTypeFactory;
        $this->typeDefs = $typeDefs;
    }

    /**
     * Build a new ComplexType object
     *
     * @param int $recordType The record type, can be indicated using the RecordTypes enum
     *
     * @return ComplexType
     */
    public function create($recordType)
    {
        $typeDef = $typeDefs->getTypeDefinition($recordType);
        $type = $this->complexTypeFactory->create($typeDef);

        if ($typeDef !== null) {
        }

        return $type;
    }
}
