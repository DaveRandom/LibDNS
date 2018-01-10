<?php declare(strict_types=1);
/**
 * Creates ResourceBuilder objects
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

use DaveRandom\LibDNS\Records\Types\TypeBuilder;
use DaveRandom\LibDNS\Records\Types\TypeFactory;
use DaveRandom\LibDNS\Records\TypeDefinitions\TypeDefinitionManager;
use DaveRandom\LibDNS\Records\TypeDefinitions\TypeDefinitionFactory;
use DaveRandom\LibDNS\Records\TypeDefinitions\FieldDefinitionFactory;

/**
 * Creates ResourceBuilder objects
 *
 * @category LibDNS
 * @package Records
 * @author Chris Wright <https://github.com/DaveRandom>
 */
class ResourceBuilderFactory
{
    /**
     * Create a new ResourceBuilder object
     *
     * @param \DaveRandom\LibDNS\Records\TypeDefinitions\TypeDefinitionManager $typeDefinitionManager
     * @return \DaveRandom\LibDNS\Records\ResourceBuilder
     */
    public function create(TypeDefinitionManager $typeDefinitionManager = null): ResourceBuilder
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
