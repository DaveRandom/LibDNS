<?php
/**
 * Represents a data type comprising multiple simple types
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
 * Represents a data type comprising multiple simple types
 *
 * @category   LibDNS
 * @package    DataTypes
 * @author     Chris Wright <https://github.com/DaveRandom>
 */
class ComplexType extends DataType implements \Iterator, \Countable
{
    /**
     * @var \LibDNS\DataTypes\SimpleType[] The items that make up the complex type
     */
    private $fields = [];

    /**
     * @var int[] Structural definition of the fields
     */
    private $typeDef;

    /**
     * @var bool Whether the iteration pointer has more elements to yield
     */
    private $pointerValid = 0;

    /**
     * Constructor
     *
     * @param int[] $typeDef Structural definition of the fields
     */
    public function __construct(array $typeDef = null)
    {
        $this->typeDef = $typeDef;
    }

    /**
     * Get the field indicated by the supplied index
     *
     * @param int $index The field index
     *
     * @return \LibDNS\DataTypes\SimpleType
     *
     * @throws \OutOfBoundsException When the supplied index does not refer to a valid field
     */
    public function getField($index)
    {
        if (!isset($this->fields[$index])) {
            throw new \OutOfBoundsException('Index ' . $index . ' does not refer to a valid field');
        }

        return $this->fields[$index];
    }

    /**
     * Get the field indicated by the supplied index
     *
     * @param int                          $index The field index
     * @param \LibDNS\DataTypes\SimpleType $value The field value
     *
     * @throws \OutOfBoundsException     When the supplied index does not refer to a valid field
     * @throws \InvalidArgumentException When the supplied value does not match the type definition
     */
    public function setField($index, SimpleType $value)
    {
        if (isset($this->typeDef)) {
            if (!isset($this->typeDef[$index])) {
                throw new \OutOfBoundsException('Index ' . $index . ' does not refer to a valid field');
            }

            if (
                   ($this->typeDef[$index] === SimpleTypes::ANYTHING && !($value instanceof Anything))
                || ($this->typeDef[$index] === SimpleTypes::BITMAP && !($value instanceof BitMap))
                || ($this->typeDef[$index] === SimpleTypes::CHAR && !($value instanceof Char))
                || ($this->typeDef[$index] === SimpleTypes::CHARACTER_STRING && !($value instanceof CharacterString))
                || ($this->typeDef[$index] === SimpleTypes::DOMAIN_NAME && !($value instanceof DomainName))
                || ($this->typeDef[$index] === SimpleTypes::IPV4_ADDRESS && !($value instanceof IPv4Address))
                || ($this->typeDef[$index] === SimpleTypes::IPV6_ADDRESS && !($value instanceof IPv6Address))
                || ($this->typeDef[$index] === SimpleTypes::LONG && !($value instanceof Long))
                || ($this->typeDef[$index] === SimpleTypes::SHORT && !($value instanceof Short))
            ) {
                throw new \InvalidArgumentException('Value data type does not match type definition');
            }
        }

        return $this->fields[$index];
    }

    /**
     * Get the structural definition of the fields
     *
     * @return int[]
     */
    public function getTypeDef($index)
    {
        return $this->typeDef;
    }

    /**
     * Get the field indicated by the iteration pointer (Iterator interface)
     *
     * @return \LibDNS\DataTypes\SimpleType
     *
     * @throws \OutOfBoundsException When the pointer does not refer to a valid field
     */
    public function current()
    {
        return current($this->fields);
    }

    /**
     * Get the value of the iteration pointer (Iterator interface)
     *
     * @return Record
     */
    public function key()
    {
        return key($this->fields);
    }

    /**
     * Increment the iteration pointer (Iterator interface)
     */
    public function next()
    {
        $this->pointerValid = next($this->fields) !== false;
    }

    /**
     * Reset the iteration pointer to the beginning (Iterator interface)
     */
    public function rewind()
    {
        reset($this->fields);
        $this->pointerValid = count($this->fields) > 0;
    }

    /**
     * Test whether the iteration pointer indicates a valid field (Iterator interface)
     *
     * @return bool
     */
    public function valid()
    {
        return $this->pointerValid;
    }

    /**
     * Get the number of fields (Countable interface)
     *
     * @return int
     */
    public function count()
    {
        return count($this->fields);
    }
}
