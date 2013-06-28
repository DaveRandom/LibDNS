<?php
/**
 * Factory which creates Message objects
 *
 * PHP version 5.4
 *
 * @category   LibDNS
 * @package    Messages
 * @author     Chris Wright <https://github.com/DaveRandom>
 * @copyright  Copyright (c) Chris Wright <https://github.com/DaveRandom>
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version    2.0.0
 */
namespace LibDNS\Messages;

use \LibDNS\Records\RecordCollectionFactory;

/**
 * Factory which creates Message objects
 *
 * @category   LibDNS
 * @package    Messages
 * @author     Chris Wright <https://github.com/DaveRandom>
 */
class MessageFactory
{
    /**
     * @var \LibDNS\Records\RecordCollectionFactory Factory which creates RecordCollection objects
     */
    private $recordCollectionFactory;

    /**
     * Constructor
     *
     * @param \LibDNS\Records\RecordCollectionFactory $recordCollectionFactory Factory which creates RecordCollection objects
     */
    public function __construct(RecordCollectionFactory $recordCollectionFactory)
    {
        $this->recordCollectionFactory = $recordCollectionFactory;
    }

    /**
     * Create a new Message object
     *
     * @return \LibDNS\Messages\Message
     */
    public function create()
    {
        return new Message($this->recordCollectionFactory);
    }
}
