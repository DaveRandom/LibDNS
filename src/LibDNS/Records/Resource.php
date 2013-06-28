<?php
/**
 * Represents a DNS resource record
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

use \LibDNS\DataTypes\DataType,
    \LibDNS\DataTypes\SimpleType,
    \LibDNS\DataTypes\ComplexType,
    \LibDNS\DataTypes\Anything,
    \LibDNS\DataTypes\BitMap,
    \LibDNS\DataTypes\Char,
    \LibDNS\DataTypes\CharacterString,
    \LibDNS\DataTypes\DomainName,
    \LibDNS\DataTypes\IPv4Address,
    \LibDNS\DataTypes\IPv6Address,
    \LibDNS\DataTypes\Long,
    \LibDNS\DataTypes\Short,
    \LibDNS\DataTypes\SimpleTypes;

/**
 * Represents a DNS resource record
 *
 * @category   LibDNS
 * @package    Records
 * @author     Chris Wright <https://github.com/DaveRandom>
 */
class Resource extends Record
{
    /**
     * @var int Value of the resource's time-to-live property
     */
    private $ttl;

    /**
     * @var \LibDNS\DataTypes\DataType
     */
    private $data;

    /**
     * @var int|int[]|null Structure of the resource RDATA section
     */
    private $typeDef;

    /**
     * Assert that a simpletype object is of the subtype indicated by the numeric index
     *
     * @param int                          $type  Data type index of the resource, can be indicated using the SimpleTypes enum
     * @param \LibDNS\DataTypes\SimpleType $value Object to inspect
     *
     * @return bool
     */
    private function assertSimpleType($type, SimpleType $value)
    {
        return ($type === SimpleTypes::ANYTHING         && $value instanceof Anything)
            || ($type === SimpleTypes::BITMAP           && $value instanceof BitMap)
            || ($type === SimpleTypes::CHAR             && $value instanceof Char)
            || ($type === SimpleTypes::CHARACTER_STRING && $value instanceof CharacterString)
            || ($type === SimpleTypes::DOMAIN_NAME      && $value instanceof DomainName)
            || ($type === SimpleTypes::IPV4_ADDRESS     && $value instanceof IPv4Address)
            || ($type === SimpleTypes::IPV6_ADDRESS     && $value instanceof IPv6Address)
            || ($type === SimpleTypes::LONG             && $value instanceof Long)
            || ($type === SimpleTypes::SHORT            && $value instanceof Short);
    }

    /**
     * Constructor
     *
     * @param int            $type    Record type of the resource, can be indicated using the RecordTypes enum
     * @param int|int[]|null $typeDef Structure of the resource RDATA section
     */
    public function __construct($type, $typeDef)
    {
        $this->type = $type;
        $this->typeDef = $typeDef;
    }

    /**
     * Get the value of the record TTL field
     *
     * @return int
     */
    public function getTTL()
    {
        return $this->ttl;
    }

    /**
     * Set the value of the record TTL field
     *
     * @param int $ttl The new value
     *
     * @throws \RangeException When the supplied value is outside the valid range 0 - 4294967296
     */
    public function setTTL($ttl)
    {
        $ttl = (int) $ttl;
        if ($ttl < 0 || $ttl > 4294967296) {
            throw new \RangeException('Record class must be in the range 0 - 4294967296');
        }

        $this->ttl = $ttl;
    }

    /**
     * Get the value of the record data field
     *
     * @return \LibDNS\DataTypes\DataType
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set the value of the record data field
     *
     * @param \LibDNS\DataTypes\DataType $data The new value
     *
     * @throws \InvalidArgumentException When the supplied data does not match the resource type definition
     */
    public function setData(DataType $data)
    {
        if (isset($this->typeDef)) {
            if (is_array($this->typeDef)) {
                if (!($data instanceof ComplexType)) {
                    throw new \InvalidArgumentException('Supplied data does not match the resource type definition');
                }

                foreach ($this->typeDef as $index => $fieldType) {
                    if (!$this->assertSimpleType($fieldType, $data->getField($index))) {
                        throw new \InvalidArgumentException('Supplied data does not match the resource type definition');
                    }
                }
            } else if (!$this->assertSimpleType($this->typeDef, $data)) {
                throw new \InvalidArgumentException('Supplied data does not match the resource type definition');
            }
        }

        $this->data = $data;
    }
}
