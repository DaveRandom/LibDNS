<?php
/**
 * Creates ResourceBuilder objects
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

use \LibDNS\Records\Types\TypeBuilder,
    \LibDNS\Records\Types\TypeFactory,
    \LibDNS\Records\TypeDefinitions\TypeDefinitionManager,
    \LibDNS\Records\TypeDefinitions\TypeDefinitionFactory,
    \LibDNS\Records\TypeDefinitions\FieldDefinitionFactory;

/**
 * Creates ResourceBuilder objects
 *
 * @category   LibDNS
 * @package    Records
 * @author     Chris Wright <https://github.com/DaveRandom>
 */
class ResourceBuilderFactory
{
    /**
     * Create a new ResourceBuilder object
     *
     * @param \LibDNS\Records\TypeDefinitions\TypeDefinitionManager $typeDefinitionManager
     *
     * @return \LibDNS\Records\ResourceBuilder
     */
    public function create(TypeDefinitionManager $typeDefinitionManager = null)
    {
        return new ResourceBuilder(
            new ResourceFactory,
            new RDataBuilder(
                new RDataFactory,
                new TypeBuilder(new TypeFactory)
            ),
            $typeDefinitionManager ?: new TypeDefinitionManager(
                new TypeDefinitionFactory,
                new FieldDefinitionFactory
            )
        );
    }
}
