<?php
/**
 * Creates Resource objects
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

use \LibDNS\Records\Types\TypeFactory;

/**
 * Creates Resource objects
 *
 * @category LibDNS
 * @package Records
 * @author Chris Wright <https://github.com/DaveRandom>
 */
class ResourceFactory
{
    /**
     * Create a new Resource object
     *
     * @param int $type Can be indicated using the ResourceTypes enum
     * @param \LibDNS\Records\RData $data
     * @return \LibDNS\Records\Resource
     */
    public function create($type, $data)
    {
        return new Resource(new TypeFactory, $type, $data);
    }
}
