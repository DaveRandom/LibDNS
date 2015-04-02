<?php
/**
 * Represents a raw network data packet
 *
 * PHP version 5.4
 *
 * @category LibDNS
 * @package Packets
 * @author Chris Wright <https://github.com/DaveRandom>
 * @copyright Copyright (c) Chris Wright <https://github.com/DaveRandom>
 * @license http://www.opensource.org/licenses/mit-license.html MIT License
 * @version 2.0.0
 */
namespace LibDNS\Packets;

/**
 * Represents a raw network data packet
 *
 * @category LibDNS
 * @package Packets
 * @author Chris Wright <https://github.com/DaveRandom>
 */
class Packet
{
    /**
     * @var string
     */
    private $data;

    /**
     * @var int Data length
     */
    private $length;

    /**
     * @var int Read pointer
     */
    private $pointer = 0;

    /**
     * Constructor
     *
     * @param string $data The initial packet raw data
     */
    public function __construct($data = '')
    {
        $this->data = (string) $data;
        $this->length = strlen($this->data);
    }

    /**
     * Read bytes from the packet data
     *
     * @param int $length The number of bytes to read
     * @return string
     * @throws \OutOfBoundsException When the pointer position is invalid or the supplied length is negative
     */
    public function read($length = null)
    {
        if ($this->pointer > $this->length) {
            throw new \OutOfBoundsException('Pointer position invalid');
        }

        if ($length === null) {
            $result = substr($this->data, $this->pointer);
            $this->pointer = $this->length;
        } else {
            $length = (int) $length;
            if ($length < 0) {
                throw new \OutOfBoundsException('Length must be a positive integer');
            }

            $result = substr($this->data, $this->pointer, (int) $length);
            $this->pointer += $length;
        }

        return $result;
    }

    /**
     * Append data to the packet
     *
     * @param string $data The data to append
     * @return int The number of bytes written
     */
    public function write($data)
    {
        $length = strlen($data);

        $this->data .= $data;
        $this->length += $length;

        return $length;
    }

    /**
     * Reset the read pointer
     */
    public function reset()
    {
        $this->pointer = 0;
    }

    /**
     * Get the pointer index
     *
     * @return int
     */
    public function getPointer()
    {
        return $this->pointer;
    }

    /**
     * Get the data length
     *
     * @return int
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * Get the number of remaining bytes from the pointer position
     *
     * @return int
     */
    public function getBytesRemaining()
    {
        return $this->length - $this->pointer;
    }
}
