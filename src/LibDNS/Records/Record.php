<?php
/**
 * Represents a DNS record
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

use \LibDNS\DataTypes\DomainName;

/**
 * Represents a DNS record
 *
 * @category   LibDNS
 * @package    Records
 * @author     Chris Wright <https://github.com/DaveRandom>
 */
abstract class Record
{
    /**
     * @var \LibDNS\DataTypes\DomainName
     */
    protected $name;

    /**
     * @var int
     */
    protected $type;

    /**
     * @var int
     */
    protected $class;

    /**
     * Get the value of the record name field
     *
     * @return \LibDNS\DataTypes\DomainName
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of the record name field
     *
     * @param \LibDNS\DataTypes\DomainName $name The new value
     */
    public function setName(DomainName $name)
    {
        $this->name = $name;
    }

    /**
     * Get the value of the record type field
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get the value of the record class field
     *
     * @return int
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Set the value of the record class field
     *
     * @param int $class The new value, can be indicated using the RecordClasses/RecordQClasses enums
     *
     * @throws \RangeException When the supplied value is outside the valid range 0 - 65535
     */
    public function setClass($class)
    {
        $class = (int) $class;
        if ($class < 0 || $class > 65535) {
            throw new \RangeException('Record class must be in the range 0 - 65535');
        }

        $this->class = $class;
    }
}
