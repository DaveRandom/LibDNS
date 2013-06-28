<?php
/**
 * Creates Resource objects
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
 * Creates Resource objects
 *
 * @category   LibDNS
 * @package    Records
 * @author     Chris Wright <https://github.com/DaveRandom>
 */
class ResourceFactory
{
    /**
     * Create a new Resource object
     *
     * @param int            $type    Record type of the resource, can be indicated using the RecordTypes enum
     * @param int|int[]|null $typeDef Structure of the resource RDATA section
     *
     * @return \LibDNS\Records\Resource
     */
    public function create($type, $typeDef)
    {
        return new Resource($type, $typeDef);
    }
}
