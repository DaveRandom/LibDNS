<?php
/**
 * Base class for simple data types
 *
 * PHP version 5.4
 *
 * @category LibDNS
 * @package Types
 * @author Chris Wright <https://github.com/DaveRandom>
 * @copyright Copyright (c) Chris Wright <https://github.com/DaveRandom>
 * @license http://www.opensource.org/licenses/mit-license.html MIT License
 * @version 2.0.0
 */
namespace LibDNS\Records\Types;

/**
 * Base class for simple data types
 *
 * @category LibDNS
 * @package Types
 * @author Chris Wright <https://github.com/DaveRandom>
 */
abstract class Type
{
    /**
     * @var mixed The internal value
     */
    protected $value;

    /**
     * Constructor
     *
     * @param mixed $value Internal value
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
     * @throws \RuntimeException When the supplied value is invalid
     */
    abstract public function setValue($value);
}
