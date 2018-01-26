<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Messages;

use DaveRandom\LibDNS\Records\QuestionRecord;
use DaveRandom\LibDNS\Records\ResourceRecord;
use const DaveRandom\LibDNS\UINT16_MASK;

abstract class Message
{
    const HEADER_SIZE = 12;
    const FLAGS_MASK = MessageFlags::IS_RESPONSE
                     | MessageFlags::IS_AUTHORITATIVE
                     | MessageFlags::IS_TRUNCATED
                     | MessageFlags::IS_RECURSION_DESIRED
                     | MessageFlags::IS_RECURSION_AVAILABLE;

    private $id;
    private $opCode;
    private $flags;
    private $responseCode;

    private $questionRecords = [];
    private $answerRecords;
    private $authorityRecords;
    private $additionalRecords;

    protected function __construct(
        int $id,
        int $flags,
        int $opCode,
        int $responseCode,
        array $questionRecords,
        array $answerRecords,
        array $authorityRecords,
        array $additionalRecords
    ) {
        if (($id & UINT16_MASK) !== $id) {
            throw new \InvalidArgumentException('Message ID must be in the range 0 - 65535');
        }

        if (($opCode & 0xf) !== $opCode) {
            throw new \InvalidArgumentException('Opcode must be in the range 0 - 15');
        }

        if (($responseCode & 0xf) !== $responseCode) {
            throw new \InvalidArgumentException('Response code must be in the range 0 - 15');
        }

        $this->id = $id;
        $this->flags = $flags & self::FLAGS_MASK;
        $this->opCode = $opCode;
        $this->responseCode = $responseCode;
        $this->questionRecords = $questionRecords;
        $this->answerRecords = $answerRecords;
        $this->authorityRecords = $authorityRecords;
        $this->additionalRecords = $additionalRecords;
    }

    final public function getId(): int
    {
        return $this->id;
    }

    final public function getFlags(): int
    {
        return $this->flags;
    }

    final public function isResponse(): bool
    {
        return (bool)($this->flags & MessageFlags::IS_RESPONSE);
    }

    final public function isAuthoritative(): bool
    {
        return (bool)($this->flags & MessageFlags::IS_AUTHORITATIVE);
    }

    final public function isTruncated(): bool
    {
        return (bool)($this->flags & MessageFlags::IS_TRUNCATED);
    }

    final public function isRecursionDesired(): bool
    {
        return (bool)($this->flags & MessageFlags::IS_RECURSION_DESIRED);
    }

    final public function isRecursionAvailable(): bool
    {
        return (bool)($this->flags & MessageFlags::IS_RECURSION_AVAILABLE);
    }

    final public function getOpCode(): int
    {
        return $this->opCode;
    }

    final public function getResponseCode(): int
    {
        return $this->opCode;
    }

    /**
     * @return QuestionRecord[]
     */
    final public function getQuestionRecords(): array
    {
        return $this->questionRecords;
    }

    /**
     * @return ResourceRecord[]
     */
    final public function getAnswerRecords(): array
    {
        return $this->answerRecords;
    }

    /**
     * @return ResourceRecord[]
     */
    final public function getAuthorityRecords(): array
    {
        return $this->authorityRecords;
    }

    /**
     * @return ResourceRecord[]
     */
    final public function getAdditionalRecords(): array
    {
        return $this->additionalRecords;
    }
}
