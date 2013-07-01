<?php
/**
 * Defines a data type comprising multiple simple types
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
 * Defines a data type comprising multiple simple types
 *
 * @category   LibDNS
 * @package    DataTypes
 * @author     Chris Wright <https://github.com/DaveRandom>
 */
class DataTypeDefinition implements \Iterator, \Countable
{
    /**
     * @var FieldDefinitionFactory Creates FieldDefinition objects
     */
    private $fieldDefFactory;

    /**
     * @var int Whether the type definition allows the last field to consist of an arbitrary number of values (>0 indicates minimum number)
     */
    private $fieldCount;

    /**
     * @var \LibDNS\DataTypes\FieldDefinition The last field defined by the data type
     */
    private $lastField;

    /**
     * @var int[] Map of field indexes to type identifiers
     */
    private $fieldDefs = [];

    /**
     * @var string[] Map of field indexes to names
     */
    private $fieldNameMap = [];

    /**
     * @var int[] Map of field names to indexes
     */
    private $fieldIndexMap = [];

    /**
     * @var bool Whether the iteration pointer indicates a valid item
     */
    private $pointerValid = true;

    /**
     * Constructor
     *
     * @param FieldDefinitionFactory $fieldDefFactory
     * @param int[]                  $typeDef         Structural definition of the fields
     *
     * @throws \InvalidArgumentException When the type definition is invalid
     */
    public function __construct(FieldDefinitionFactory $fieldDefFactory, array $typeDef = null)
    {
        if ($typeDef !== null) {
            $this->typeDef = $typeDef;
            $this->fieldCount = count($typeDef);

            $index = 0;
            foreach ($typeDef as $name => $type) {
                $this->registerField($index++, $name, $type);
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
    private function registerField($index, $name, $type)
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

            $allowsMultiple = true;
            $minimumValues = (int) $matches['minimum'];
        } else {
            $allowsMultiple = false;
            $minimumValues = 0;
        }

        $this->fieldDefs[$index] = $this->fieldDefFactory->create($index, $matches['name'], $type, $allowsMultiple, $minimumValues);
        if ($index === $this->fieldCount - 1) {
            $this->lastField = $this->fieldDefs[$index];
        }

        $this->fieldIndexMap[$matches['name']] = $index;
        $this->fieldNameMap[$index] = $matches['name'];
    }

    /**
     * Get the field name indicated by the supplied index
     *
     * @param int $index
     *
     * @return string
     *
     * @throws \OutOfBoundsException When the supplied index does not refer to a valid field
     */
    public function getFieldNameFromIndex($index)
    {
        if (!isset($this->fieldNameMap[$index])) {
            throw new \OutOfBoundsException('Index ' . $index . ' does not refer to a valid field');
        }

        return $this->fieldNameMap[$index];
    }

    /**
     * Get the field index indicated by the supplied name
     *
     * @param string $name
     *
     * @return int
     *
     * @throws \OutOfBoundsException When the supplied name does not refer to a valid field
     */
    public function getFieldIndexFromName($name)
    {
        $name = strtolower($name);
        if (!isset($this->fieldIndexMap[$name])) {
            throw new \OutOfBoundsException('Name ' . $name . ' does not refer to a valid field');
        }

        return $this->fieldIndexMap[$name];
    }

    /**
     * Assert that the specified value is valid at the specified index
     *
     * @param int                          $index
     * @param \LibDNS\DataTypes\SimpleType $value
     *
     * @return bool
     */
    public function assertValidByIndex($index, SimpleType $value)
    {
        $index = (int) $index;
        if (isset($this->fieldDefs[$index])) {
            $fieldDef = $this->fieldDefs[$index];
        } else if ($index >= 0 && $this->lastField->allowsMultiple()) {
            $fieldDef = $this->lastField;
        } else {
            return false;
        }

        return $this->assertSimpleType($fieldDef->getType(), $value);
    }

    /**
     * Assert that the specified value is valid at the specified index
     *
     * @param string                       $name
     * @param \LibDNS\DataTypes\SimpleType $value
     *
     * @return bool
     */
    public function assertValidByName($name, SimpleType $value)
    {
        try {
            $index = $this->getFieldIndexFromName($name);
        } catch (\OutOfBoundsException $e) {
            return false;
        }

        return $this->assertValidByIndex($index, $value);
    }

    /**
     * Get the field indicated by the iteration pointer (Iterator interface)
     *
     * @return \LibDNS\DataTypes\FieldDefinition
     */
    public function current()
    {
        return current($this->fieldDefs);
    }

    /**
     * Get the key indicated by the iteration pointer
     *
     * @return int
     */
    public function key()
    {
        return key($this->fieldDefs);
    }

    /**
     * Increment the iteration pointer (Iterator interface)
     */
    public function next()
    {
        $this->pointerValid = next($this->fieldDefs) !== false;
    }

    /**
     * Reset the iteration pointer to the beginning (Iterator interface)
     */
    public function rewind()
    {
        reset($this->fieldDefs);
        $this->pointerValid = count($this->fieldDefs) > 0;
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
        return $this->fieldCount;
    }
}
