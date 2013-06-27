<?php
/**
 * Class representing a character string
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
 * Class representing a character string
 *
 * @category   LibDNS
 * @package    DataTypes
 * @author     Chris Wright <https://github.com/DaveRandom>
 */
class CharacterString extends SimpleType
{
    /**
     * @var string The internal value
     */
    protected $value = '';

    /**
     * Set the internal value
     *
     * @param string $value The new value
     *
     * @throws \UnexpectedValueException When the supplied value is outside the valid length range 0 - 255
     */
    public function setValue($value)
    {
        $value = (string) $value;
        if (strlen($value) > 255) {
            throw new \UnexpectedValueException('Character string length must be in the range 0 - 255');
        }

        $this->value = $value;
    }
}
