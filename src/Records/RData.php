<?php
/**
 * Represents the RDATA section of a resource record
 *
 * PHP version 5.4
 *
 * @category LibDNS
 * @package Records
 * @author Chris Wright <https://github.com/DaveRandom>
 * @copyright Copyright (c) Chris Wright <https://github.com/DaveRandom>
 * @license http://www.opensource.org/licenses/mit-license.html MIT License
 * @version 2.0.0
 */
namespace LibDNS\Records;

use \LibDNS\Records\Types\Type;
use \LibDNS\Records\TypeDefinitions\TypeDefinition;

/**
 * Represents a data type comprising multiple simple types
 *
 * @category LibDNS
 * @package Records
 * @author Chris Wright <https://github.com/DaveRandom>
 */
class RData implements \Iterator, \Countable
{
    /**
     * @var \LibDNS\Records\Types\Type[] The items that make up the complex type
     */
    private $fields = [];

    /**
     * @var \LibDNS\Records\TypeDefinitions\TypeDefinition Structural definition of the fields
     */
    private $typeDef;

    /**
     * @var bool Whether the iteration pointer has more elements to yield
     */
    private $pointerValid = 0;

    /**
     * Constructor
     *
     * @param \LibDNS\Records\TypeDefinitions\TypeDefinition $typeDef
     */
    public function __construct(TypeDefinition $typeDef)
    {
        $this->typeDef = $typeDef;
    }

    /**
     * Magic method for type coersion to string
     *
     * @return string
     */
    public function __toString()
    {
        if ($handler = $this->typeDef->getToStringFunction()) {
            $result = call_user_func_array($handler, $this->fields);
        } else {
            $result = implode(',', $this->fields);
        }

        return $result;
    }

    /**
     * Get the field indicated by the supplied index
     *
     * @param int $index
     * @return \LibDNS\Records\Types\Type
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
     * Set the field indicated by the supplied index
     *
     * @param int $index
     * @param \LibDNS\Records\Types\Type $value
     * @throws \InvalidArgumentException When the supplied index/value pair does not match the type definition
     */
    public function setField($index, Type $value)
    {
        if (!$this->typeDef->getFieldDefinition($index)->assertDataValid($value)) {
            throw new \InvalidArgumentException('The supplied value is not valid for the specified index');
        }

        $this->fields[$index] = $value;
    }

    /**
     * Get the field indicated by the supplied name
     *
     * @param string $name
     * @return \LibDNS\Records\Types\Type
     * @throws \OutOfBoundsException When the supplied name does not refer to a valid field
     */
    public function getFieldByName($name)
    {
        return $this->getField($this->typeDef->getFieldIndexByName($name));
    }

    /**
     * Set the field indicated by the supplied name
     *
     * @param string $name
     * @param \LibDNS\Records\Types\Type $value
     * @throws \OutOfBoundsException When the supplied name does not refer to a valid field
     * @throws \InvalidArgumentException When the supplied value does not match the type definition
     */
    public function setFieldByName($name, Type $value)
    {
        $this->setField($this->typeDef->getFieldIndexByName($name), $value);
    }

    /**
     * Get the structural definition of the fields
     *
     * @return \LibDNS\Records\TypeDefinitions\TypeDefinition
     */
    public function getTypeDefinition()
    {
        return $this->typeDef;
    }

    /**
     * Get the field indicated by the iteration pointer (Iterator interface)
     *
     * @return \LibDNS\Records\Types\Type
     */
    public function current()
    {
        return current($this->fields);
    }

    /**
     * Get the value of the iteration pointer (Iterator interface)
     *
     * @return int
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
