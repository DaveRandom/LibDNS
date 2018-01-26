<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Records\ResourceData;

use DaveRandom\LibDNS\Records\ResourceData;
use DaveRandom\Network\DomainName;

final class MX implements ResourceData
{
    const TYPE_ID = 15;

    private $preference;
    private $exchange;

    public function __construct(int $preference, DomainName $exchange)
    {
        $this->preference = $preference;
        $this->exchange = $exchange;
    }

    public function getPreference(): int
    {
        return $this->preference;
    }

    public function getExchange(): DomainName
    {
        return $this->exchange;
    }

    public function getTypeId(): int
    {
        return self::TYPE_ID;
    }
}
