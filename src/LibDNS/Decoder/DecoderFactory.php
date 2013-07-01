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
    \LibDNS\DataTypes\DataTypeDefinitions,
    \LibDNS\DataTypes\DataTypeFactory,
    \LibDNS\DataTypes\DataTypeBuilder;

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
    public function create()
    {
        $dataTypeFactory = new DataTypeFactory;

        return new Decoder(
            new PacketFactory,
            new MessageFactory(new RecordCollectionFactory),
            new QuestionFactory,
            new ResourceBuilder(
                new ResourceFactory,
                new DataTypeDefinitions,
                new DataTypeBuilder($dataTypeFactory)
            ),
            $dataTypeFactory,
            new DecodingContextFactory
        );
    }
}
