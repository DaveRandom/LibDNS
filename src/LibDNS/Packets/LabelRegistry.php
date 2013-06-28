<?php
/**
 * Maintains a list of the relationships between domain name labels and the first point at
 * which they appear in a packet
 *
 * PHP version 5.4
 *
 * @category   LibDNS
 * @package    Packets
 * @author     Chris Wright <https://github.com/DaveRandom>
 * @copyright  Copyright (c) Chris Wright <https://github.com/DaveRandom>
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version    2.0.0
 */
namespace LibDNS\Packets;

/**
 * Creates Packet objects
 *
 * @category   LibDNS
 * @package    Packets
 * @author     Chris Wright <https://github.com/DaveRandom>
 */
class LabelRegistry
{
    /**
     * @var int[] Map of labels to indexes
     */
    private $labels = [];

    /**
     * @var string[] Map of indexes to labels
     */
    private $indexes = [];

    /**
     * Register a new relationship
     *
     * @param string $label
     * @param int $index
     */
    public function register($label, $index)
    {
        if (!isset($this->labels[$label]) || $index < $this->labels[$label]) {
            $this->labels[$label] = $index;
        }

        $this->indexes[$index] = $label;
    }

    /**
     * Lookup the index of a label
     *
     * @param string $label
     *
     * @return int|null
     */
    public function lookupIndex($label)
    {
        return isset($this->labels[$label]) ? $this->labels[$label] : null;
    }

    /**
     * Lookup the label at an index
     *
     * @param int $index
     *
     * @return string|null
     */
    public function lookupLabel($index)
    {
        return isset($this->indexes[$index]) ? $this->indexes[$index] : null;
    }
}
