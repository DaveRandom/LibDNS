<?php
/**
 * Class representing an IPv6 address
 *
 * PHP version 5.4
 *
 * @category   LibDNS
 * @package    DataTypes
 * @author     Chris Wright <https://github.com/DaveRandom>
 * @copyright  Copyright (c) Chris Wright <https://github.com/DaveRandom>
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version    2.0.0
 */
namespace LibDNS\DataTypes;

/**
 * Class representing an IPv6 address
 *
 * @category   LibDNS
 * @package    DataTypes
 * @author     Chris Wright <https://github.com/DaveRandom>
 */
class IPv6Address extends SimpleType
{
    /**
     * @var string The internal value
     */
    protected $value = '::';

    /**
     * @var int[] The shorts of the address
     */
    private $shorts = [0, 0, 0, 0, 0, 0, 0, 0];

    /**
     * Create a compressed string representation of an IPv6 address
     *
     * @param int[] $shorts Address shorts
     *
     * @return string
     */
    private function createCompressedString($shorts)
    {
        $compressLen = $compressPos = $currentLen = $currentPos = 0;
        $inBlock = false;
        
        for ($i = 0; $i < 8; $i++) {
            if ($shorts[$i] === 0) {
                if (!$inBlock) {
                    $inBlock = true;
                    $currentPos = $i;
                }

                $currentLen++;
            } else if ($inBlock) {
                if ($currentLen > $compressLen) {
                    $compressLen = $currentLen;
                    $compressPos = $currentPos;
                }

                $inBlock = false;
                $currentPos = $currentLen = 0;
            }
        }
        if ($inBlock) {
            $compressLen = $currentLen;
            $compressPos = $currentPos;
        }

        if ($compressLen === 8) {
            return '::';
        }

        if ($compressLen > 1) {
            $replace = $compressPos === 0 || $compressPos + $compressLen === 8 ? ['', ''] : [''];
            array_splice($shorts, $compressPos, $compressLen, $replace);
        }

        return implode(':', array_map(function($short) {
            return $short === '' ? '' : dechex($short);
        }, $shorts));
    }

    /**
     * Constructor
     *
     * @param string[] $labels Label list
     *
     * @throws \UnexpectedValueException When the supplied value is not a valid domain name
     */
    public function __construct($value = null)
    {
        if (isset($value)) {
            if (is_array($value)) {
                $this->setShorts($value);
            } else {
                $this->setValue($value);
            }
        }
    }

    /**
     * Set the internal value
     *
     * @param string $value The new value
     *
     * @throws \UnexpectedValueException When the supplied value is outside the valid length range 0 - 65535
     */
    public function setValue($value)
    {
        $this->setShorts(explode('.', $value));
    }

    /**
     * Get the address shorts
     *
     * @return int[]
     */
    public function getShorts()
    {
        return $this->shorts;
    }

    /**
     * Set the address shorts
     *
     * @param int[] $shorts The new address shorts
     *
     * @throws \UnexpectedValueException When the supplied short list is not a valid IPv6 address
     */
    public function setShorts(array $shorts)
    {
        if (count($shorts) !== 8) {
            throw new \UnexpectedValueException('Short list is not a valid IPv6 address: invalid short count');
        }

        foreach ($shorts as &$short) {
            if (!ctype_digit((string) $short) || $short < 0 || $short > 65535) {
                throw new \UnexpectedValueException('Short list is not a valid IPv6 address: invalid short value ' . $short);
            }

            $short = (int) $short;
        }

        $this->shorts = array_values($shorts);
        $this->value = $this->createCompressedString($this->shorts);
    }
}
