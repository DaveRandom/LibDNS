<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Messages;

final class Response extends Message
{
    public function __construct(int $id, int $flags, int $opCode, int $responseCode, array $questionRecords, array $answerRecords, array $authorityRecords, array $additionalRecords)
    {
        parent::__construct($id, $flags | MessageFlags::IS_RESPONSE, $opCode, $responseCode, $questionRecords, $answerRecords, $authorityRecords, $additionalRecords);
    }
}
