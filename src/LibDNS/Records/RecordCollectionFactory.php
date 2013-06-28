<?php
/**
 * Creates RecordCollection objects
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

/**
 * Creates RecordCollection objects
 *
 * @category   LibDNS
 * @package    Records
 * @author     Chris Wright <https://github.com/DaveRandom>
 */
class RecordCollectionFactory
{
    /**
     * Create a new RecordCollection object
     *
     * @return \LibDNS\Records\RecordCollection
     */
    public function create()
    {
        return new RecordCollection;
    }
}
