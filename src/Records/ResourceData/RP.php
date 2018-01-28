<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Records\ResourceData;

use DaveRandom\LibDNS\Protocol\DecodingContext;
use DaveRandom\LibDNS\Protocol\EncodingContext;
use DaveRandom\LibDNS\Records\ResourceData;
use DaveRandom\LibDNS\Records\ResourceTypes;
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

    public function getTypeId(): int
    {
        return ResourceTypes::RP;
    }

    public static function decode(DecodingContext $ctx): RP
    {
        $mailboxName = \DaveRandom\LibDNS\decode_domain_name($ctx);
        $txtName = \DaveRandom\LibDNS\decode_domain_name($ctx);

        return new RP($mailboxName, $txtName);
    }

    public static function encode(EncodingContext $ctx, RP $record)
    {
        \DaveRandom\LibDNS\encode_domain_name($ctx, $record->mailboxName);
        \DaveRandom\LibDNS\encode_domain_name($ctx, $record->txtName);
    }
}
