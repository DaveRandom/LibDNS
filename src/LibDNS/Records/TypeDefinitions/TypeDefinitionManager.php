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
        ResourceTypes::A => [ // RFC 1035
            'address' => Types::IPV4_ADDRESS,
        ],
        ResourceTypes::AAAA  => [ // RFC 3596
            'address' => Types::IPV6_ADDRESS,
        ],
        ResourceTypes::AFSDB => [ // RFC 1183
            'subtype'  => Types::SHORT,
            'hostname' => Types::DOMAIN_NAME,
        ],
        ResourceTypes::CNAME => [ // RFC 1035
            'cname' => Types::DOMAIN_NAME,
        ],
        ResourceTypes::HINFO => [ // RFC 1035
            'cpu' => Types::CHARACTER_STRING,
            'os'  => Types::CHARACTER_STRING,
        ],
        ResourceTypes::ISDN => [ // RFC 1183
            'isdn-address' => Types::CHARACTER_STRING,
            'sa'           => Types::CHARACTER_STRING,
        ],
        ResourceTypes::MB => [ // RFC 1035
            'madname' => Types::DOMAIN_NAME,
        ],
        ResourceTypes::MD => [ // RFC 1035
            'madname' => Types::DOMAIN_NAME,
        ],
        ResourceTypes::MF => [ // RFC 1035
            'madname' => Types::DOMAIN_NAME,
        ],
        ResourceTypes::MG => [ // RFC 1035
            'mgmname' => Types::DOMAIN_NAME,
        ],
        ResourceTypes::MINFO => [ // RFC 1035
            'rmailbx' => Types::DOMAIN_NAME,
            'emailbx' => Types::DOMAIN_NAME,
        ],
        ResourceTypes::MR => [ // RFC 1035
            'newname' => Types::DOMAIN_NAME,
        ],
        ResourceTypes::MX => [ // RFC 1035
            'preference' => Types::SHORT,
            'exchange'   => Types::DOMAIN_NAME,
        ],
        ResourceTypes::NS => [ // RFC 1035
            'nsdname' => Types::DOMAIN_NAME,
        ],
        ResourceTypes::NULL => [ // RFC 1035
            'data' => Types::ANYTHING,
        ],
        ResourceTypes::PTR => [ // RFC 1035
            'ptrdname' => Types::DOMAIN_NAME,
        ],
        ResourceTypes::RP => [ // RFC 1183
            'mbox-dname' => Types::DOMAIN_NAME,
            'txt-dname'  => Types::DOMAIN_NAME,
        ],
        ResourceTypes::RT => [ // RFC 1183
            'preference'        => Types::SHORT,
            'intermediate-host' => Types::DOMAIN_NAME,
        ],
        ResourceTypes::SOA => [ // RFC 1035
            'mname'      => Types::DOMAIN_NAME,
            'rname'      => Types::DOMAIN_NAME,
            'serial'     => Types::LONG,
            'refresh'    => Types::LONG,
            'retry'      => Types::LONG,
            'expire'     => Types::LONG,
            'minimum'    => Types::LONG,
            '__toString' => function() {
            },
        ],
        ResourceTypes::TXT => [ // RFC 1035
            'txtdata+' => Types::CHARACTER_STRING,
        ],
        ResourceTypes::WKS => [ // RFC 1035
            'address'  => Types::IPV4_ADDRESS,
            'protocol' => Types::SHORT,
            'bit-map'  => Types::BITMAP,
        ],
        ResourceTypes::X25 => [ // RFC 1183
            'psdn-address' => Types::CHARACTER_STRING,
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
            $definition = isset($this->definitions[$recordType]) ? $this->definitions[$recordType] : ['data' => Types::ANYTHING];
            $this->typeDefs[$recordType] = $this->typeDefFactory->create($this->fieldDefFactory, $definition);
        }

        return $this->typeDefs[$recordType];
    }

    /**
     * Register a custom type definition
     *
     * @param int                                                  $resourceType Resource type, can be indicated using the ResourceTypes enum
     * @param int[]|\LibDNS\Records\TypeDefinitions\TypeDefinition $definition
     *
     * @throws \InvalidArgumentException When the type definition is invalid
     */
    public function registerTypeDefinition($recordType, $definition)
    {
        if (!($definition instanceof TypeDefinition)) {
            if (!is_array($definition)) {
                throw new \InvalidArgumentException('Definition must be an array or an instance of ' . __NAMESPACE__ . '\TypeDefinition');
            }

            $typeDef = (int) $this->typeDefFactory->create($this->fieldDefFactory, $definition);
        }

        $this->typeDefs[(int) $recordType] = $typeDef;
    }
}
