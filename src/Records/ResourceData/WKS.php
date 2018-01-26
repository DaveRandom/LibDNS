<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Records\ResourceData;

use DaveRandom\LibDNS\Records\ResourceData;
use DaveRandom\Network\IPv4Address;

final class WKS implements ResourceData
{
    const TYPE_ID = 11;

    private $address;
    private $protocol;
    private $bitMap;

    public function __construct(IPv4Address $address, int $protocol, string $bitMap)
    {
        if (\strlen($bitMap) > 8192) {
            throw new \InvalidArgumentException('Bit map must be no more than 65536 bits');
        }

        $this->address = $address;
        $this->protocol = $protocol;
        $this->bitMap = \rtrim($bitMap, "\x00");
    }

    public function getAddress(): IPv4Address
    {
        return $this->address;
    }

    public function getProtocol(): int
    {
        return $this->protocol;
    }

    public function getBitMap(): string
    {
        return $this->bitMap;
    }

    public function hasPort(int $port): bool
    {
        return (bool)(\ord($this->bitMap[(int)($port / 8)] ?? "\x00") & (1 << (7 - ($port % 8))));
    }

    public function getTypeId(): int
    {
        return self::TYPE_ID;
    }
}
