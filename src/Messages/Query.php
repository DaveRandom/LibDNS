<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Messages;

final class Query extends Message
{
    /** @internal */
    const FLAGS_MASK = MessageFlags::IS_RECURSION_DESIRED
                     | MessageFlags::IS_TRUNCATED
                     | MessageFlags::IS_RECURSION_DESIRED
                     | MessageFlags::IS_DNSSEC_CHECKING_DISABLED
                     | MessageFlags::IS_DNSSEC_AUTHENTIC_DATA;

    public function __construct(
        array $questionRecords,
        int $id = 0,
        int $flags = MessageFlags::IS_RECURSION_DESIRED,
        int $opCode = MessageOpCodes::QUERY
    ) {
        parent::__construct($id, $flags & self::FLAGS_MASK, $opCode, 0, $questionRecords, [], [], []);
    }
}
