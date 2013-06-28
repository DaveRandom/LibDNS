<?php
/**
 * Creates Parser objects
 *
 * PHP version 5.4
 *
 * @category   LibDNS
 * @package    Parser
 * @author     Chris Wright <https://github.com/DaveRandom>
 * @copyright  Copyright (c) Chris Wright <https://github.com/DaveRandom>
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version    2.0.0
 */
namespace LibDNS\Parser;

use \LibDNS\MessageFactory,
    \LibDNS\Records\RecordCollectionFactory,
    \LibDNS\Records\QuestionFactory,
    \LibDNS\Records\ResourceFactory,
    \LibDNS\DataTypes\DataTypeDefinitions,
    \LibDNS\DataTypes\DataTypeFactory,
    \LibDNS\DataTypes\DataTypeBuilder;

/**
 * Creates Parser objects
 *
 * @category   LibDNS
 * @package    Parser
 * @author     Chris Wright <https://github.com/DaveRandom>
 */
class ParserFactory
{
    /**
     * Create a new Parser object
     *
     * @return \LibDNS\Parser\Parser
     */
    public function create()
    {
        return new Parser(
            new MessageFactory(new RecordCollectionFactory),
            new QuestionFactory,
            new ResourceBuilder(
                new ResourceFactory,
                new DataTypeDefinitions,
                new DataTypeBuilder(new DataTypeFactory)
            )
        );
    }
}
