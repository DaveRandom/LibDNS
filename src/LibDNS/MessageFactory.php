<?php
/**
 * Factory which creates Message objects
 *
 * PHP version 5.4
 *
 * @category   LibDNS
 * @package    LibDNS
 * @author     Chris Wright <https://github.com/DaveRandom>
 * @copyright  Copyright (c) Chris Wright <https://github.com/DaveRandom>
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version    2.0.0
 */
namespace LibDNS;

use \LibDNS\Record\RecordCollectionFactory;

/**
 * Factory which creates Message objects
 *
 * @category   LibDNS
 * @package    LibDNS
 * @author     Chris Wright <https://github.com/DaveRandom>
 */
class MessageFactory
{
    /**
     * @var RecordCollectionFactory Factory which creates RecordCollection objects
     */
    private $recordCollectionFactory;

    /**
     * Constructor
     *
     * @param RecordCollectionFactory $recordCollectionFactory Factory which creates RecordCollection objects
     */
    public function __construct(RecordCollectionFactory $recordCollectionFactory)
    {
        $this->recordCollectionFactory = $recordCollectionFactory;
    }

    /**
     * Create a new Message object
     *
     * @return Message
     */
    public function create()
    {
        return new Message($this->recordCollectionFactory);
    }
}
