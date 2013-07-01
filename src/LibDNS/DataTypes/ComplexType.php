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
     * @var int Whether the type definition allows the last field to consist of an arbitrary number of values (>0 indicates minimum number)
     */
    private $fieldCount;

    /**
     * @var bool Whether the type definition allows the last field to consist of multiple values
     */
    private $allowMultipleLast = false;

    /**
     * @var int Minimum number of values for the last field
     */
    private $lastFieldMinimumValues;

    /**
     * @var int[] Map of field indexes to type identifiers
     */
    private $typeMap = [];

    /**
     * @var int[] Map of field indexes to names
     */
    private $fieldNameMap = [];

    /**
     * @var int[] Map of field names to indexes
     */
    private $fieldIndexMap = [];

    /**
     * @var bool Whether the iteration pointer has more elements to yield
     */
    private $pointerValid = 0;

    /**
     * Constructor
     *
     * @param int[] $typeDef Structural definition of the fields
     *
     * @throws \InvalidArgumentException When the type definition is invalid
     */
    public function __construct(array $typeDef = null)
    {
        if ($typeDef !== null) {
            $this->typeDef = $typeDef;
            $this->fieldCount = count($typeDef);

            $index = 0;
            foreach ($typeDef as $name => $type) {
                $this->registerTypeDefField($index++, $name, $type);
            }
        }
    }

    /**
     * Assert that a SimpleType object is of the subtype indicated by the bitmask
     *
     * @param int                          $type  Data type index of the resource, can be indicated using the SimpleTypes enum
     * @param \LibDNS\DataTypes\SimpleType $value Object to inspect
     *
     * @return bool
     */
    private function assertSimpleType($type, SimpleType $value)
    {
        return (($type & SimpleTypes::ANYTHING)         && $value instanceof Anything)
            || (($type & SimpleTypes::BITMAP)           && $value instanceof BitMap)
            || (($type & SimpleTypes::CHAR)             && $value instanceof Char)
            || (($type & SimpleTypes::CHARACTER_STRING) && $value instanceof CharacterString)
            || (($type & SimpleTypes::DOMAIN_NAME)      && $value instanceof DomainName)
            || (($type & SimpleTypes::IPV4_ADDRESS)     && $value instanceof IPv4Address)
            || (($type & SimpleTypes::IPV6_ADDRESS)     && $value instanceof IPv6Address)
            || (($type & SimpleTypes::LONG)             && $value instanceof Long)
            || (($type & SimpleTypes::SHORT)            && $value instanceof Short);
    }

   /**
     * Register a field from the type definition
     *
     * @param int    $index
     * @param string $name
     * @param int    $name
     *
     * @throws \InvalidArgumentException When the field definition is invalid
     */
    private function registerTypeDefField($index, $name, $type)
    {
        if (!preg_match('/^(?P<name>[\w\-]+)(?P<quantifier>\+|\*)?(?P<minimum>(?<=\+)\d+)?$/', strtolower($name), $matches)) {
            throw new \InvalidArgumentException('Invalid field definition ' . $name . ': Syntax error');
        }

        if (isset($matches['quantifier'])) {
            if ($index !== $this->fieldCount - 1) {
                throw new \InvalidArgumentException('Invalid field definition ' . $name . ': Quantifiers only allowed in last field');
            }

            if (!isset($matches['minimum'])) {
                $matches['minimum'] = $matches['quantifier'] === '+' ? 1 : 0;
            }

            $this->allowMultipleLast = true;
            $this->lastFieldMinimumValues = $matches['minimum'];
        }

        $this->typeMap[$index] = $type;
        $this->fieldNameMap[$matches['name']] = $index;
        $this->fieldIndexMap[$index] = $matches['name'];
    }

    /**
     * Get the field indicated by the supplied index
     *
     * @param int $index
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
     * Set the field indicated by the supplied index
     *
     * @param int                          $index
     * @param \LibDNS\DataTypes\SimpleType $value
     *
     * @throws \OutOfBoundsException     When the supplied index does not refer to a valid field
     * @throws \InvalidArgumentException When the supplied value does not match the type definition
     */
    public function setField($index, SimpleType $value)
    {
        if (isset($this->typeDef)) {
            if (!isset($this->typeMap[$index])) {
                throw new \OutOfBoundsException('Index ' . $index . ' does not refer to a valid field');
            }

            if (!$this->assertSimpleType($this->typeMap[$index], $value)) {
                throw new \InvalidArgumentException('Value data type does not match type definition');
            }
        }

        $this->fields[$index] = $value;
    }

    /**
     * Get the field indicated by the supplied name
     *
     * @param string $name
     *
     * @return \LibDNS\DataTypes\SimpleType
     *
     * @throws \OutOfBoundsException When the supplied name does not refer to a valid field
     */
    public function getFieldByName($name)
    {
        $fieldName = strtolower($name);
        if (!isset($this->fieldNameMap[$fieldName])) {
            throw new \OutOfBoundsException('Name ' . $name . ' does not refer to a valid field');
        }

        return $this->getField($this->fieldNameMap[$fieldName]);
    }

    /**
     * Set the field indicated by the supplied name
     *
     * @param string                       $name
     * @param \LibDNS\DataTypes\SimpleType $value
     *
     * @throws \OutOfBoundsException     When the supplied name does not refer to a valid field
     * @throws \InvalidArgumentException When the supplied value does not match the type definition
     */
    public function setFieldByName($name, SimpleType $value)
    {
        $fieldName = strtolower($name);
        if (!isset($this->fieldNameMap[$fieldName])) {
            throw new \OutOfBoundsException('Name ' . $name . ' does not refer to a valid field');
        }

        $this->setField($this->fieldNameMap[$fieldName], $value);
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
     */
    public function current()
    {
        return current($this->fields);
    }

    /**
     * Get the value of the iteration pointer (Iterator interface)
     *
     * @return string
     */
    public function key()
    {
        return $this->fieldIndexMap[key($this->fields)];
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
