<?php declare(strict_types=1);
/**
 * Represents a fully qualified domain name
 *
 * PHP version 5.4
 *
 * @category LibDNS
 * @package Types
 * @author Chris Wright <https://github.com/DaveRandom>
 * @copyright Copyright (c) Chris Wright <https://github.com/DaveRandom>
 * @license http://www.opensource.org/licenses/mit-license.html MIT License
 * @version 2.0.0
 */
namespace LibDNS;

/**
 * @category LibDNS
 */
class NormalizedLabel
{
    /**
     * @var string
     */
    private $label;

    /**
     * Constructor
     *
     * @param string $label
     */
    public function __construct(string $label)
    {
        if (\function_exists('idn_to_ascii')) {
            if (false === $result = \idn_to_ascii($label, 0, INTL_IDNA_VARIANT_UTS46)) {
                throw new \InvalidArgumentException("Label '{$label}' could not be processed for IDN");
            }
        } else {
            if (\preg_match('/[\x80-\xff]/', $label)) {
                throw new \InvalidArgumentException(
                    "Label '{$label}' contains non-ASCII characters and IDN support is not available."
                    . " Verify that ext/intl is installed for IDN support."
                );
            }
        }

        $labelLength = \strlen($label);
        if ($labelLength > 63) {
            throw new \InvalidArgumentException(
                'Label list is not a valid domain name: Label ' . $label . ' length exceeds 63 byte limit'
            );
        }

        $this->label = \strtolower($label);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->label;
    }

}