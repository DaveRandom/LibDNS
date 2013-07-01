<?php
/**
 * Builds DataTypes from type definitions
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
 * Builds DataTypes from type definitions
 *
 * @category   LibDNS
 * @package    DataTypes
 * @author     Chris Wright <https://github.com/DaveRandom>
 */
class DataTypeBuilder
{
    /**
     * @var \LibDNS\DataTypes\DataTypeFactory
     */
    private $dataTypeFactory;

    /**
     * Create a SimpleType object from a numeric type ID
     *
     * @param int $dataType The data type, can be indicated using the SimpleTypes enum
     *
     * @return \LibDNS\DataTypes\SimpleType
     */
    private function createSimpleType($dataType)
    {
        if ($dataType === SimpleTypes::ANYTHING) {
            $result = $this->dataTypeFactory->createAnything();
        } else if ($dataType === SimpleTypes::BITMAP) {
            $result = $this->dataTypeFactory->createBitMap();
        } else if ($dataType === SimpleTypes::CHAR) {
            $result = $this->dataTypeFactory->createChar();
        } else if ($dataType === SimpleTypes::CHARACTER_STRING) {
            $result = $this->dataTypeFactory->createCharacterString();
        } else if ($dataType === SimpleTypes::DOMAIN_NAME) {
            $result = $this->dataTypeFactory->createDomainName();
        } else if ($dataType === SimpleTypes::IPV4_ADDRESS) {
            $result = $this->dataTypeFactory->createIPv4Address();
        } else if ($dataType === SimpleTypes::IPV6_ADDRESS) {
            $result = $this->dataTypeFactory->createIPv6Address();
        } else if ($dataType === SimpleTypes::LONG) {
            $result = $this->dataTypeFactory->createLong();
        } else if ($dataType === SimpleTypes::SHORT) {
            $result = $this->dataTypeFactory->createShort();
        }
        
        return $result;
    }

    /**
     * Constructor
     *
     * @param \LibDNS\DataTypes\DataTypeFactory $dataTypeFactory
     */
    public function __construct(DataTypeFactory $dataTypeFactory)
    {
        $this->dataTypeFactory = $dataTypeFactory;
    }

    /**
     * Build a new DataType object corresponding to a resource record type
     *
     * @param int $recordType Resource type, can be indicated using the ResourceTypes enum
     *
     * @return \LibDNS\DataTypes\DataType
     */
    public function build($recordType, $typeDef)
    {
        if (is_array($typeDef)) {
            $result = $this->dataTypeFactory->createComplexType($typeDef);
 
            foreach ($typeDef as $index => $fieldType) {
                $result->setField($index, $this->createSimpleType($fieldType));
            }
        } else {
            if ($typeDef === null) {
                $typeDef = SimpleTypes::ANYTHING;
            }

            $result = $this->createSimpleType($typeDef);
        }

        return $result;
    }
}
