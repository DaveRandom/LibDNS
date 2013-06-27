<?php
/**
 * Class representing an individual DNS resource record
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

use \LibDNS\DataTypes\DataType;

/**
 * Class representing an individual DNS resource record
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
     * @var DataType Data associated with the record
     */
    private $data;

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
     * @return DataType
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set the value of the record data field
     *
     * @param DataType $data The new value
     */
    public function setData(DataType $data)
    {
        $this->data = $data;
    }
}
