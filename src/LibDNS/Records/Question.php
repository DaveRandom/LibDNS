<?php
/**
 * Class representing an individual DNS question record
 *
 * PHP version 5.4
 *
 * @category   LibDNS
 * @package    Records
 * @author     Chris Wright <https://github.com/DaveRandom>
 * @copyright  Copyright (c) Chris Wright <https://github.com/DaveRandom>
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version    2.0.0
 */
namespace LibDNS\Records;

/**
 * Class representing an individual DNS question record
 *
 * @category   LibDNS
 * @package    Records
 * @author     Chris Wright <https://github.com/DaveRandom>
 */
class Question extends Record
{
    /**
     * Constructor
     *
     * @param int $type Record type being requested. Can be indicated using the RecordQTypes enum
     */
    public function __construct($type)
    {
        $this->type = $type;
    }
}
