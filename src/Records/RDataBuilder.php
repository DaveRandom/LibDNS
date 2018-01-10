<?php declare(strict_types=1);
/**
 * Builds RData objects from a type definition
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

use DaveRandom\LibDNS\Records\TypeDefinitions\TypeDefinition;
use DaveRandom\LibDNS\Records\Types\TypeBuilder;

/**
 * Creates RData objects
 *
 * @category LibDNS
 * @package Records
 * @author Chris Wright <https://github.com/DaveRandom>
 */
class RDataBuilder
{
    /**
     * @var \DaveRandom\LibDNS\Records\RDataFactory
     */
    private $rDataFactory;

    /**
     * @var \DaveRandom\LibDNS\Records\Types\TypeBuilder
     */
    private $typeBuilder;

    /**
     * Constructor
     *
     * @param \DaveRandom\LibDNS\Records\RDataFactory $rDataFactory
     * @param \DaveRandom\LibDNS\Records\Types\TypeBuilder $typeBuilder
     */
    public function __construct(RDataFactory $rDataFactory, TypeBuilder $typeBuilder)
    {
        $this->rDataFactory = $rDataFactory;
        $this->typeBuilder = $typeBuilder;
    }

    /**
     * Create a new RData object
     *
     * @param \DaveRandom\LibDNS\Records\TypeDefinitions\TypeDefinition $typeDefinition
     * @return \DaveRandom\LibDNS\Records\RData
     */
    public function build(TypeDefinition $typeDefinition): RData
    {
        $rData = $this->rDataFactory->create($typeDefinition);

        foreach ($typeDefinition as $index => $type) {
            $rData->setField($index, $this->typeBuilder->build($type->getType()));
        }

        return $rData;
    }
}
