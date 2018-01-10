<?php declare(strict_types=1);
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
namespace DaveRandom\LibDNS\Records;

use DaveRandom\LibDNS\Records\Types\TypeFactory;
use DaveRandom\LibDNS\Records\Resource as ResourceRecord;

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
     * @param \DaveRandom\LibDNS\Records\RData $data
     * @return \DaveRandom\LibDNS\Records\Resource
     */
    public function create(int $type, RData $data): ResourceRecord
    {
        return new Resource(new TypeFactory, $type, $data);
    }
}
