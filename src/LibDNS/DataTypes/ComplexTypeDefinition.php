<?php
/**
 * Object which holds data about how a known complex type is structured
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
 * Object which holds data about how a known complex type is structured
 *
 * @category   LibDNS
 * @package    DataTypes
 * @author     Chris Wright <https://github.com/DaveRandom>
 */
class ComplexTypeDefinition
{
    /**
     * @var array
     */
    private $typeDef;

    /**
     * Constructor
     *
     * @param array $typeDef
     */
    public function __construct(array $typeDef)
    {
        $this->typeDef = $typeDef;
    }
}
