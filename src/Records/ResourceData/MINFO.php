<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Records\ResourceData;

use DaveRandom\LibDNS\Records\ResourceData;

final class MINFO implements ResourceData
{
    const TYPE_ID = 14;

    private $responsibleMailbox;
    private $errorMailbox;

    public function __construct(string $responsibleMailbox, string $errorMailbox)
    {
        $this->responsibleMailbox = $responsibleMailbox;
        $this->errorMailbox = $errorMailbox;
    }

    public function getResponsibleMailbox(): string
    {
        return $this->responsibleMailbox;
    }

    public function getErrorMailbox(): string
    {
        return $this->errorMailbox;
    }

    public function getTypeId(): int
    {
        return self::TYPE_ID;
    }
}
