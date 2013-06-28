<?php
/**
 * Parses raw network data to Message objects
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

use \LibDNS\Messages\MessageFactory,
    \LibDNS\Records\QuestionFactory,
    \LibDNS\Records\ResourceBuilder;

/**
 * Parses raw network data to Message objects
 *
 * @category   LibDNS
 * @package    Parser
 * @author     Chris Wright <https://github.com/DaveRandom>
 */
class Parser
{
    /**
     * @var \LibDNS\Messages\MessageFactory
     */
    private $messageFactory;

    /**
     * @var \LibDNS\Records\QuestionFactory
     */
    private $questionFactory;

    /**
     * @var \LibDNS\Records\ResourceBuilder
     */
    private $resourceBuilder;

    /**
     * Constructor
     *
     * @param \LibDNS\Messages\MessageFactory $messageFactory
     * @param \LibDNS\Records\QuestionFactory $questionFactory
     * @param \LibDNS\Records\ResourceBuilder $resourceBuilder
     */
    public function __construct(MessageFactory $messageFactory, QuestionFactory $questionFactory, ResourceBuilder $resourceBuilder)
    {
        $this->messageFactory = $messageFactory;
        $this->questionFactory = $questionFactory;
        $this->resourceBuilder = $resourceBuilder;
    }

    /**
     * Parse a Message from raw network data
     *
     * @param string $data The data string to parse
     *
     * @return \LibDNS\Messages\Message
     */
    public function parse($data)
    {
    }
}
