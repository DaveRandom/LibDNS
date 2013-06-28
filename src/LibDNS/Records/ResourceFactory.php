<?php
/**
 * Factory which creates Resource objects
 *
 * PHP version 5.4
 *
 * @category   LibDNS
 * @package    Records
 * @author     Chris Wright <https://github.com/DaveRandom>
 * @copyright  Copyright (c) Chris Wright <https://github.com/DaveRandom>
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version    2.0.0
 */
namespace LibDNS\Records;

use \LibDNS\DataTypes\DataTypeDefinitions,
    \LibDNS\DataTypes\DataTypeFactory,
    \LibDNS\DataTypes\DataTypeBuilder;

/**
 * Factory which creates Resource objects
 *
 * @category   LibDNS
 * @package    Records
 * @author     Chris Wright <https://github.com/DaveRandom>
 */
class ResourceFactory
{
    /**
     * @var DataTypeDefinitions Object which holds data about how the RDATA sections of known resource record types are structured
     */
    private $dataTypeDefinitions;

    /**
     * @var DataTypeBuilder Class for objects which build data types from type definitions
     */
    private $dataTypeBuilder;

    /**
     * Constructor
     *
     * @param DataTypeDefinitions $dataTypeDefinitions Holds data about how the RDATA sections of known resource record types are structured
     * @param DataTypeBuilder $dataTypeBuilder Builds DataType objects from type definitions
     */
    public function __construct(DataTypeDefinitions $dataTypeDefinitions, DataTypeBuilder $dataTypeBuilder)
    {
        $this->dataTypeDefinitions = $dataTypeDefinitions;
        $this->dataTypeBuilder = $dataTypeBuilder;
    }

    /**
     * Create a new Resource object
     *
     * @return Resource
     */
    public function create($type)
    {
        $typeDef = $this->dataTypeDefinitions->getTypeDefinition($type);

        $resource = new Resource($type, $typeDef);
        $resource->setData($this->dataTypeBuilder->build($type, $typeDef));

        return $resource;
    }
}
