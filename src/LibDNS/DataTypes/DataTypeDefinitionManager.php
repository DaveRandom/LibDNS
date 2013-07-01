<?php
/**
 * Holds data about how the RDATA sections of known resource record types are structured
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

use \LibDNS\Records\ResourceTypes;

/**
 * Holds data about how the RDATA sections of known resource record types are structured
 *
 * @category   LibDNS
 * @package    DataTypes
 * @author     Chris Wright <https://github.com/DaveRandom>
 */
class DataTypeDefinitionManager
{
    /**
     * @var array How the RDATA sections of known resource record types are structured
     */
    private $definitions =
    [
        ResourceTypes::A     => SimpleTypes::IPV4_ADDRESS,
        ResourceTypes::AAAA  => SimpleTypes::IPV6_ADDRESS,
        ResourceTypes::AFSDB => ,
        ResourceTypes::CNAME => SimpleTypes::DOMAIN_NAME,
        ResourceTypes::HINFO => [
            'cpu' => SimpleTypes::CHARACTER_STRING,
            'os'  => SimpleTypes::CHARACTER_STRING,
        ],
        ResourceTypes::ISDN  => ,
        ResourceTypes::MB    => SimpleTypes::DOMAIN_NAME,
        ResourceTypes::MD    => SimpleTypes::DOMAIN_NAME,
        ResourceTypes::MF    => SimpleTypes::DOMAIN_NAME,
        ResourceTypes::MG    => SimpleTypes::DOMAIN_NAME,
        ResourceTypes::MINFO => [
            'rmailbx' => SimpleTypes::DOMAIN_NAME,
            'emailbx' => SimpleTypes::DOMAIN_NAME,
        ],
        ResourceTypes::MR    => SimpleTypes::DOMAIN_NAME,
        ResourceTypes::MX    => [
            'preference' => SimpleTypes::SHORT,
            'exchange'   => SimpleTypes::DOMAIN_NAME,
        ],
        ResourceTypes::NS    => SimpleTypes::DOMAIN_NAME,
        ResourceTypes::NULL  => SimpleTypes::ANYTHING,
        ResourceTypes::PTR   => SimpleTypes::DOMAIN_NAME,
        ResourceTypes::RP    => ,
        ResourceTypes::RT    => ,
        ResourceTypes::SOA   => [
            'mname'   => SimpleTypes::DOMAIN_NAME,
            'rname'   => SimpleTypes::DOMAIN_NAME,
            'serial'  => SimpleTypes::LONG,
            'refresh' => SimpleTypes::LONG,
            'retry'   => SimpleTypes::LONG,
            'expire'  => SimpleTypes::LONG,
            'minimum' => SimpleTypes::LONG,
        ],
        ResourceTypes::TXT   => [
            'txtdata+' => SimpleTypes::CHARACTER_STRING
        ],
        ResourceTypes::WKS   => ,
        ResourceTypes::X25   => ,
    ];

    /**
     * @var array Cache of created definitions
     */
    private $typeDefs = [];

    /**
     * @var \LibDNS\DataTypes\DataTypeDefinitionFactory
     */
    private $typeDefFactory;

    /**
     * @var \LibDNS\DataTypes\FieldDefinitionFactory
     */
    private $fieldDefFactory;

    /**
     * Constructor
     *
     * @param \LibDNS\DataTypes\DataTypeDefinitionFactory $typeDefFactory
     * @param \LibDNS\DataTypes\FieldDefinitionFactory    $fieldDefFactory
     */
    public function __construct(DataTypeDefinitionFactory $typeDefFactory, FieldDefinitionFactory $fieldDefFactory)
    {
        $this->typeDefFactory = $typeDefFactory;
        $this->fieldDefFactory = $fieldDefFactory;
    }

    /**
     * Get a type definition for a record type if it is known
     *
     * @param int $recordType Resource type, can be indicated using the ResourceTypes enum
     *
     * @return int|\LibDNS\DataTypes\DataTypeDefinition
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
                $this->typeDefs[$recordType] = SimpleTypes::ANYTHING;
            }
        }

        return $this->typeDefs[$recordType];
    }

    /**
     * Register a custom type definition
     *
     * @param int                                      $recordType Resource type, can be indicated using the ResourceTypes enum
     * @param int|\LibDNS\DataTypes\DataTypeDefinition $typeDef
     */
    public function registerTypeDefinition($recordType, $typeDef)
    {
        $recordType = (int) $recordType;
        if (!($typeDef instanceof DataTypeDefinition)) {
            $typeDef = (int) $typeDef;
        }

        $this->typeDefs[$recordType] = $typeDef;
    }
}
