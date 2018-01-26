<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Records\ResourceData;

use DaveRandom\Network\DomainName;

final class NS
{
    const TYPE_ID = 2;

    private $authoritativeServerName;

    public function __construct(DomainName $authoritativeServerName)
    {
        $this->authoritativeServerName = $authoritativeServerName;
    }

    public function getAuthoritativeServerName(): DomainName
    {
        return $this->authoritativeServerName;
    }

    public function getTypeId(): int
    {
        return self::TYPE_ID;
    }
}
