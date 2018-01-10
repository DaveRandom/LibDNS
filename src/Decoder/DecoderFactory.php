<?php declare(strict_types=1);
/**
 * Creates Decoder objects
 *
 * PHP version 5.4
 *
 * @category LibDNS
 * @package Decoder
 * @author Chris Wright <https://github.com/DaveRandom>
 * @copyright Copyright (c) Chris Wright <https://github.com/DaveRandom>
 * @license http://www.opensource.org/licenses/mit-license.html MIT License
 * @version 2.0.0
 */
namespace DaveRandom\LibDNS\Decoder;

use DaveRandom\LibDNS\Records\ResourceBuilder;
use DaveRandom\LibDNS\Records\TypeDefinitions\TypeDefinitionManager;
use DaveRandom\LibDNS\Records\Types\TypeBuilder;
use DaveRandom\LibDNS\Records\Types\TypeFactory;

/**
 * Creates Decoder objects
 *
 * @category LibDNS
 * @package Decoder
 * @author Chris Wright <https://github.com/DaveRandom>
 */
class DecoderFactory
{
    /**
     * Create a new Decoder object
     *
     * @param \DaveRandom\LibDNS\Records\TypeDefinitions\TypeDefinitionManager $typeDefinitionManager
     * @param bool $allowTrailingData
     * @return Decoder
     */
    public function create(TypeDefinitionManager $typeDefinitionManager = null, bool $allowTrailingData = true): Decoder
    {
        $typeBuilder = new TypeBuilder(new TypeFactory);

        return new Decoder(
            new ResourceBuilder($typeBuilder, $typeDefinitionManager ?? new TypeDefinitionManager),
            $typeBuilder,
            $allowTrailingData
        );
    }
}
