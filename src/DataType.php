<?php

  namespace DaveRandom\DNS;

  use \DaveRandom\DNS\Packet;

  abstract class DataType implements IEntity {

    const DATATYPE_DOMAINNAME = 1;
    const DATATYPE_INETADDR   = 2;
    const DATATYPE_CHAR       = 3;
    const DATATYPE_SHORT      = 4;
    const DATATYPE_LONG       = 5;
    const DATATYPE_CHARSTRING = 6;
    const DATATYPE_BITMAP     = 7;
    const DATATYPE_ANYTHING   = 8;
    const DATATYPE_VECTOR     = 99;

    private static $dataTypes = array(
      1 => 'InetAddress',     // A
      2 => 'DomainName',      // NS
      3 => 'DomainName',      // MD
      4 => 'DomainName',      // MF
      5 => 'DomainName',      // CNAME
      6 => 'Vectors\\SOA',    // SOA
      7 => 'DomainName',      // MB
      8 => 'DomainName',      // MG
      9 => 'DomainName',      // MR
      10 => 'Anything',       // NULL
      11 => 'Vectors\\WKS',   // WKS
      12 => 'DomainName',     // PTR
      13 => 'Vectors\\HINFO', // HINFO
      14 => 'Vectors\\MINFO', // MINFO
      15 => 'Vectors\\MX',    // MX
      16 => 'CharacterString' // TXT
    );

    protected $type;

    public static function createFromPacket(Packet $packet, $dataLength, $type) {
      if (!isset(self::$dataTypes[$type])) {
        throw new \InvalidArgumentException('Invalid data type');
      }
      $class = '\\DaveRandom\\DNS\\DataTypes\\'.self::$dataTypes[$type];
      return $class::createFromPacket($packet, $dataLength);
    }

    public function __toString() {
      return (string) $this->getFormattedData();
    }

    public function getType() {
      return $this->type;
    }

    abstract public function __construct($data);

    abstract public function getRawData();

    abstract public function getFormattedData();

  }
