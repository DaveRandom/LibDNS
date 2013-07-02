<?php
/**
 * Creates RData objects
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

use \LibDNS\Records\TypeDefinitions\TypeDefinition;

/**
 * Creates RData objects
 *
 * @category   LibDNS
 * @package    Records
 * @author     Chris Wright <https://github.com/DaveRandom>
 */
class RDataFactory
{
    /**
     * Create a new RData object
     *
     * @param \LibDNS\Records\TypeDefinitions\TypeDefinition $typeDef
     *
     * @return \LibDNS\Records\RData
     */
    public function create(TypeDefinition $typeDefinition)
    {
        return new RData($typeDefinition);
    }
}
