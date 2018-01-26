<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Messages;

final class Query extends Message
{
    private static $FLAGS_MASK = MessageFlags::IS_RECURSION_DESIRED | MessageFlags::IS_TRUNCATED;

    public function __construct(int $id, array $questionRecords, int $flags = MessageFlags::IS_RECURSION_DESIRED, int $opCode = MessageOpCodes::QUERY)
    {
        parent::__construct($id, $flags & self::$FLAGS_MASK, $opCode, 0, $questionRecords, [], [], []);
    }
}
