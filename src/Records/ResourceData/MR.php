<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Records\ResourceData;

use DaveRandom\LibDNS\Records\ResourceData;
use DaveRandom\Network\DomainName;

final class MR implements ResourceData
{
    const TYPE_ID = 9;

    private $mailboxName;

    public function __construct(DomainName $mailboxName)
    {
        $this->mailboxName = $mailboxName;
    }

    public function getMailboxName(): DomainName
    {
        return $this->mailboxName;
    }

    public function getTypeId(): int
    {
        return self::TYPE_ID;
    }
}
