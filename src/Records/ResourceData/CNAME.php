<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Records\ResourceData;

use DaveRandom\LibDNS\Records\ResourceData;
use DaveRandom\Network\DomainName;

final class CNAME implements ResourceData
{
    const TYPE_ID = 5;

    private $canonicalName;

    public function __construct(DomainName $canonicalName)
    {
        $this->canonicalName = $canonicalName;
    }

    public function getCanonicalName(): DomainName
    {
        return $this->canonicalName;
    }

    public function getTypeId(): int
    {
        return self::TYPE_ID;
    }
}
