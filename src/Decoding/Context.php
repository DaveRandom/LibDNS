<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Decoding;

use DaveRandom\LibDNS\DecodingContext;

final class Context implements DecodingContext
{
    /** @var string */
    public $data;

    /** @var int */
    public $dataLength;

    /** @var int */
    public $offset;

    /** @var string[][] */
    public $labelsByIndex = [];

    /** @var int */
    public $messageId;

    /** @var int */
    public $messageOpCode;

    /** @var int */
    public $messageResponseCode;

    /** @var int */
    public $messageFlags;

    /** @var int */
    public $expectedQuestionRecords = 0;

    /** @var int */
    public $expectedAnswerRecords = 0;

    /** @var int */
    public $expectedAuthorityRecords = 0;

    /** @var int */
    public $expectedAdditionalRecords = 0;

    public function __construct(string $data, int $offset)
    {
        $this->data = $data;
        $this->dataLength = \strlen($data);
        $this->offset = $offset;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function getDataLength(): int
    {
        return $this->dataLength;
    }

    public function advanceOffset(int $length): int
    {
        $current = $this->offset;
        $this->offset += $length;

        return $current;
    }

    public function hasData(int $length): bool
    {
        return $this->offset + $length <= $this->dataLength;
    }

    public function getData(int $length): string
    {
        $result = \substr($this->data, $this->offset, $length);
        $this->offset += $length;

        return $result;
    }

    public function unpack(string $spec, int $length): array
    {
        if ($this->offset + $length > $this->dataLength) {
            throw new \UnexpectedValueException(\sprintf(
                'Decode error: Insufficient data in buffer - have &d, want %d at position 0x%X',
                $this->dataLength - $this->offset,
                $length,
                $this->offset
            ));
        }

        // use unpack() offset when min PHP version is >=7.1
        $chunk = \substr($this->data, $this->offset, $length);
        $this->offset += $length;

        return \unpack($spec, $chunk);
    }

    public function hasLabelsAtOffset(int $offset): bool
    {
        return isset($this->labelsByIndex[$offset]);
    }

    public function getLabelsAtOffset(int $offset): array
    {
        return $this->labelsByIndex[$offset];
    }

    public function setLabelsAtOffset(int $offset, array $labels)
    {
        $this->labelsByIndex[$offset] = $labels;
    }
}
