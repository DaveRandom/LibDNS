<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Records\ResourceData;

use DaveRandom\LibDNS\Protocol\DecodingContext;
use DaveRandom\LibDNS\Protocol\EncodingContext;
use DaveRandom\LibDNS\Records\ResourceData;
use DaveRandom\Network\DomainName;

final class RP implements ResourceData
{
    private $mailboxName;
    private $txtName;

    public function __construct(DomainName $mailboxName, DomainName $txtName)
    {
        $this->mailboxName = $mailboxName;
        $this->txtName = $txtName;
    }

    public function getMailboxName(): DomainName
    {
        return $this->mailboxName;
    }

    public function getTxtName(): DomainName
    {
        return $this->txtName;
    }

    public function __toString(): string
    {
        return self::zoneFileEncode($this);
    }

    public static function zoneFileEncode(self $record): string
    {
        return "{$record->mailboxName}. {$record->txtName}.";
    }

    public static function protocolDecode(DecodingContext $ctx): self
    {
        $mailboxName = \DaveRandom\LibDNS\decode_domain_name($ctx);
        $txtName = \DaveRandom\LibDNS\decode_domain_name($ctx);

        return new self($mailboxName, $txtName);
    }

    public static function protocolEncode(EncodingContext $ctx, self $record)
    {
        \DaveRandom\LibDNS\encode_domain_name($ctx, $record->mailboxName);
        \DaveRandom\LibDNS\encode_domain_name($ctx, $record->txtName);
    }
}
