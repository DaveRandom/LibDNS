<?php declare(strict_types=1);
/**
 * Builds Resource objects of a specific type
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

use DaveRandom\LibDNS\Records\TypeDefinitions\TypeDefinitionManager;
use DaveRandom\LibDNS\Records\Resource as ResourceRecord;
use DaveRandom\LibDNS\Records\Types\TypeBuilder;

/**
 * Builds Resource objects of a specific type
 *
 * @category LibDNS
 * @package Records
 * @author Chris Wright <https://github.com/DaveRandom>
 */
class ResourceBuilder
{
    /**
     * @var \DaveRandom\LibDNS\Records\Types\TypeBuilder
     */
    private $typeBuilder;

    /**
     * @var \DaveRandom\LibDNS\Records\TypeDefinitions\TypeDefinitionManager
     */
    private $typeDefinitionManager;

    /**
     * Constructor
     *
     * @param \DaveRandom\LibDNS\Records\Types\TypeBuilder $typeBuilder
     * @param \DaveRandom\LibDNS\Records\TypeDefinitions\TypeDefinitionManager $typeDefinitionManager
     */
    public function __construct(TypeBuilder $typeBuilder, TypeDefinitionManager $typeDefinitionManager)
    {
        $this->typeBuilder = $typeBuilder;
        $this->typeDefinitionManager = $typeDefinitionManager;
    }

    /**
     * Create a new Resource object
     *
     * @param int $type Type of the resource, can be indicated using the ResourceTypes enum
     * @return \DaveRandom\LibDNS\Records\Resource
     */
    public function build(int $type): ResourceRecord
    {
        $typeDefinition = $this->typeDefinitionManager->getTypeDefinition($type);

        $rData = new RData($typeDefinition);

        foreach ($typeDefinition as $index => $type) {
            $rData->setField($index, $this->typeBuilder->build($type->getType()));
        }

        return new Resource($type, $rData);
    }
}
