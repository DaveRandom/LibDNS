<?php
/**
 * Shared interface and implementations for simple data types
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
 * Shared interface and implementations for simple data types
 *
 * @category   LibDNS
 * @package    DataTypes
 * @author     Chris Wright <https://github.com/DaveRandom>
 */
abstract class SimpleType extends DataType
{
    /**
     * @var mixed The internal value
     */
    protected $value;

    /**
     * Constructor
     *
     * @param mixed $value Internal value
     *
     * @throws \RuntimeException When the supplied value is invalid
     */
    public function __construct($value = null)
    {
        if (isset($value)) {
            $this->setValue($value);
        }
    }

    /**
     * Magic method for type coersion to string
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->value;
    }

    /**
     * Get the internal value
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set the internal value
     *
     * @param mixed $value The new value
     *
     * @throws \RuntimeException When the supplied value is invalid
     */
    abstract public function setValue($value);
}
