<?php
/**
 * Holds data about how the RDATA sections of known resource record types are structured
 *
 * PHP version 5.4
 *
 * @category   LibDNS
 * @package    TypeDefinitions
 * @author     Chris Wright <https://github.com/DaveRandom>
 * @copyright  Copyright (c) Chris Wright <https://github.com/DaveRandom>
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version    2.0.0
 */
namespace LibDNS\Records\TypeDefinitions;

use \LibDNS\Records\ResourceTypes,
    \LibDNS\Records\Types\Types;

/**
 * Holds data about how the RDATA sections of known resource record types are structured
 *
 * @category   LibDNS
 * @package    TypeDefinitions
 * @author     Chris Wright <https://github.com/DaveRandom>
 */
class TypeDefinitionManager
{
    /**
     * @var array How the RDATA sections of known resource record types are structured
     */
    private $definitions = [
        ResourceTypes::A => [
            'host' => Types::IPV4_ADDRESS
        ],
        ResourceTypes::AAAA  => [
            'host' => Types::IPV6_ADDRESS
        ],
        ResourceTypes::AFSDB => [
        ],
        ResourceTypes::CNAME => [
            'name' => Types::DOMAIN_NAME
        ],
        ResourceTypes::HINFO => [
            'cpu' => Types::CHARACTER_STRING,
            'os'  => Types::CHARACTER_STRING,
        ],
        ResourceTypes::ISDN => [
        ],
        ResourceTypes::MB => [
            Types::DOMAIN_NAME
        ],
        ResourceTypes::MD => [
            Types::DOMAIN_NAME
        ],
        ResourceTypes::MF => [
            Types::DOMAIN_NAME
        ],
        ResourceTypes::MG => [
            Types::DOMAIN_NAME
        ],
        ResourceTypes::MINFO => [
            'rmailbx' => Types::DOMAIN_NAME,
            'emailbx' => Types::DOMAIN_NAME,
        ],
        ResourceTypes::MR => [
            Types::DOMAIN_NAME
        ],
        ResourceTypes::MX => [
            'preference' => Types::SHORT,
            'exchange'   => Types::DOMAIN_NAME,
        ],
        ResourceTypes::NS => [
            Types::DOMAIN_NAME
        ],
        ResourceTypes::NULL => [
            Types::ANYTHING
        ],
        ResourceTypes::PTR => [
            Types::DOMAIN_NAME
        ],
        ResourceTypes::RP => [
        ],
        ResourceTypes::RT => [
        ],
        ResourceTypes::SOA => [
            'mname'   => Types::DOMAIN_NAME,
            'rname'   => Types::DOMAIN_NAME,
            'serial'  => Types::LONG,
            'refresh' => Types::LONG,
            'retry'   => Types::LONG,
            'expire'  => Types::LONG,
            'minimum' => Types::LONG,
        ],
        ResourceTypes::TXT => [
            'txtdata+' => Types::CHARACTER_STRING
        ],
        ResourceTypes::WKS => [
        ],
        ResourceTypes::X25 => [
        ],
    ];

    /**
     * @var array Cache of created definitions
     */
    private $typeDefs = [];

    /**
     * @var \LibDNS\Records\TypeDefinitions\TypeDefinitionFactory
     */
    private $typeDefFactory;

    /**
     * @var \LibDNS\Records\TypeDefinitions\FieldDefinitionFactory
     */
    private $fieldDefFactory;

    /**
     * Constructor
     *
     * @param \LibDNS\Records\TypeDefinitions\TypeDefinitionFactory  $typeDefFactory
     * @param \LibDNS\Records\TypeDefinitions\FieldDefinitionFactory $fieldDefFactory
     */
    public function __construct(TypeDefinitionFactory $typeDefFactory, FieldDefinitionFactory $fieldDefFactory)
    {
        $this->typeDefFactory = $typeDefFactory;
        $this->fieldDefFactory = $fieldDefFactory;
    }

    /**
     * Get a type definition for a record type if it is known
     *
     * @param int $recordType Resource type, can be indicated using the ResourceTypes enum
     *
     * @return \LibDNS\Records\TypeDefinitions\TypeDefinition
     */
    public function getTypeDefinition($recordType)
    {
        $recordType = (int) $recordType;

        if (!isset($this->typeDefs[$recordType])) {
            if (isset($this->definitions[$recordType])) {
                if (is_array($this->definitions[$recordType])) {
                    $this->typeDefs[$recordType] = $this->typeDefFactory->create($this->fieldDefFactory, $this->definitions[$recordType]);
                } else {
                    $this->typeDefs[$recordType] = $this->definitions[$recordType];
                }
            } else {
                $this->typeDefs[$recordType] = Types::ANYTHING;
            }
        }

        return $this->typeDefs[$recordType];
    }

    /**
     * Register a custom type definition
     *
     * @param int                                            $recordType Resource type, can be indicated using the ResourceTypes enum
     * @param \LibDNS\Records\TypeDefinitions\TypeDefinition $typeDef
     */
    public function registerTypeDefinition($recordType, $typeDef)
    {
        $recordType = (int) $recordType;
        if (!($typeDef instanceof TypeDefinition)) {
            $typeDef = (int) $typeDef;
        }

        $this->typeDefs[$recordType] = $typeDef;
    }
}
