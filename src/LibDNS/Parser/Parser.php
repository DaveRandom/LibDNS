<?php
/**
 * Factory which creates Parser objects
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
    \LibDNS\Records\QuestionFactory,
    \LibDNS\Records\ResourceFactory;

/**
 * Factory which creates Parser objects
 *
 * @category   LibDNS
 * @package    Parser
 * @author     Chris Wright <https://github.com/DaveRandom>
 */
class Parser
{
    /**
     * @var MessageFactory Factory which creates Message objects
     */
    private $messageFactory;

    /**
     * @var QuestionFactory Factory which creates Question objects
     */
    private $questionFactory;

    /**
     * @var ResourceFactory Factory which creates Resource objects
     */
    private $resourceFactory;

    /**
     * Constructor
     *
     * @param MessageFactory $messageFactory Factory which creates Message objects
     * @param QuestionFactory $questionFactory Factory which creates Question objects
     * @param ResourceFactory $resourceFactory Factory which creates Resource objects
     */
    public function __construct(MessageFactory $messageFactory, QuestionFactory $questionFactory, ResourceFactory $resourceFactory)
    {
        $this->messageFactory = $messageFactory;
        $this->questionFactory = $questionFactory;
        $this->resourceFactory = $resourceFactory;
    }
}
