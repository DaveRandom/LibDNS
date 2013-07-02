<?php
/**
 * Creates Decoder objects
 *
 * PHP version 5.4
 *
 * @category   LibDNS
 * @package    Decoder
 * @author     Chris Wright <https://github.com/DaveRandom>
 * @copyright  Copyright (c) Chris Wright <https://github.com/DaveRandom>
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version    2.0.0
 */
namespace LibDNS\Decoder;

use \LibDNS\Packets\PacketFactory,
    \LibDNS\Messages\MessageFactory,
    \LibDNS\Records\RecordCollectionFactory,
    \LibDNS\Records\QuestionFactory,
    \LibDNS\Records\ResourceBuilder,
    \LibDNS\Records\ResourceFactory,
    \LibDNS\Records\RDataBuilder,
    \LibDNS\Records\RDataFactory,
    \LibDNS\Records\Types\TypeBuilder,
    \LibDNS\Records\Types\TypeFactory,
    \LibDNS\Records\TypeDefinitions\TypeDefinitionManager,
    \LibDNS\Records\TypeDefinitions\TypeDefinitionFactory,
    \LibDNS\Records\TypeDefinitions\FieldDefinitionFactory;

/**
 * Creates Decoder objects
 *
 * @category   LibDNS
 * @package    Decoder
 * @author     Chris Wright <https://github.com/DaveRandom>
 */
class DecoderFactory
{
    /**
     * Create a new Decoder object
     *
     * @return \LibDNS\Decoder\Decoder
     */
    public function create(TypeDefinitionManager $typeDefinitionManager = null)
    {
        $typeBuilder = new TypeBuilder(new TypeFactory);

        return new Decoder(
            new PacketFactory,
            new MessageFactory(new RecordCollectionFactory),
            new QuestionFactory,
            new ResourceBuilder(
                new ResourceFactory,
                new RDataBuilder(
                    new RDataFactory,
                    $typeBuilder
                ),
                $typeDefinitionManager ?: new TypeDefinitionManager(
                    new TypeDefinitionFactory,
                    new FieldDefinitionFactory
                )
            ),
            $typeBuilder,
            new DecodingContextFactory
        );
    }
}
