<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Records\ResourceData;

use DaveRandom\LibDNS\Records\ResourceData;
use DaveRandom\Network\DomainName;

final class PTR implements ResourceData
{
    const TYPE_ID = 12;

    private $name;

    public function __construct(DomainName $name)
    {
        $this->name = $name;
    }

    public function getName(): DomainName
    {
        return $this->name;
    }

    public function getTypeId(): int
    {
        return self::TYPE_ID;
    }
}
