<?php
/**
 * Creates DataType objects
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
 * Creates DataType objects
 *
 * @category   LibDNS
 * @package    DataTypes
 * @author     Chris Wright <https://github.com/DaveRandom>
 */
class DataTypeFactory
{
    /**
     * Create a new ComplexType object
     *
     * @param array $typeDef Type structure definition
     *
     * @return \LibDNS\DataTypes\ComplexType
     */
    public function createComplexType(array $typeDef = null)
    {
        return new ComplexType($typeDef);
    }

    /**
     * Create a new Anything object
     *
     * @param string $value
     *
     * @return \LibDNS\DataTypes\Anything
     */
    public function createAnything($value = null)
    {
        return new Anything($value);
    }

    /**
     * Create a new BitMap object
     *
     * @param string $value
     *
     * @return \LibDNS\DataTypes\BitMap
     */
    public function createBitMap($value = null)
    {
        return new BitMap($value);
    }

    /**
     * Create a new Char object
     *
     * @param int $value
     *
     * @return \LibDNS\DataTypes\Char
     */
    public function createChar($value = null)
    {
        return new Char($value);
    }

    /**
     * Create a new CharacterString object
     *
     * @param string $value
     *
     * @return \LibDNS\DataTypes\CharacterString
     */
    public function createCharacterString($value = null)
    {
        return new CharacterString($value);
    }

    /**
     * Create a new DomainName object
     *
     * @param string|string[] $value
     *
     * @return \LibDNS\DataTypes\DomainName
     */
    public function createDomainName($value = null)
    {
        return new DomainName($value);
    }

    /**
     * Create a new IPv4Address object
     *
     * @param string $value
     *
     * @return \LibDNS\DataTypes\IPv4Address
     */
    public function createIPv4Address($value = null)
    {
        return new IPv4Address($value);
    }

    /**
     * Create a new IPv6Address object
     *
     * @param string $value
     *
     * @return \LibDNS\DataTypes\IPv6Address
     */
    public function createIPv6Address($value = null)
    {
        return new IPv6Address($value);
    }

    /**
     * Create a new Long object
     *
     * @param int $value
     *
     * @return \LibDNS\DataTypes\Long
     */
    public function createLong($value = null)
    {
        return new Long($value);
    }

    /**
     * Create a new Short object
     *
     * @param int $value
     *
     * @return \LibDNS\DataTypes\Short
     */
    public function createShort($value = null)
    {
        return new Short($value);
    }
}
