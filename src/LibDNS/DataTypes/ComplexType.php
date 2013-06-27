<?php
/**
 * Class representing a complex type comprising multiple simple types
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
 * Class representing a complex type comprising multiple simple types
 *
 * @category   LibDNS
 * @package    DataTypes
 * @author     Chris Wright <https://github.com/DaveRandom>
 */
class ComplexType implements DataType, \Iterator, \Countable
{
    /**
     * @var SimpleType[] The items that make up the complex type
     */
    private $fields = [];

    /**
     * @var int Number of fields that make up the complex type
     */
    private $length = 0;

    /**
     * @var int Iteration pointer
     */
    private $position = 0;

    /**
     * @var ComplexTypeDefinition Structural definition of the complex type
     */
    private $typeDef;

    /**
     * Constructor
     *
     * @param ComplexTypeDefinition $typeDef Structural definition of the complex type
     */
    public function __construct(ComplexTypeDefinition $typeDef = null)
    {
        $this->typeDef = $typeDef;
    }

    /**
     * Get the field indicated by the iteration pointer (Iterator interface)
     *
     * @return SimpleType
     *
     * @throws \OutOfBoundsException When the pointer does not refer to a valid field
     */
    public function current()
    {
        if (!isset($this->fields[$this->position])) {
            throw new \OutOfBoundsException('The current pointer position is invalid');
        }

        return $this->fields[$this->position];
    }

    /**
     * Get the value of the iteration pointer (Iterator interface)
     *
     * @return Record
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * Increment the iteration pointer (Iterator interface)
     */
    public function next()
    {
        $this->position++;
    }

    /**
     * Reset the iteration pointer to the beginning (Iterator interface)
     */
    public function rewind()
    {
        $this->position = 0;
    }

    /**
     * Test whether the iteration pointer indicates a valid field (Iterator interface)
     *
     * @return bool
     */
    public function valid()
    {
        return isset($this->fields[$this->position]);
    }

    /**
     * Get the number of fields that make up the complex type (Countable interface)
     *
     * @return int
     */
    public function count()
    {
        return $this->length;
    }
}
