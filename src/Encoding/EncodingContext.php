<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Encoding;

use DaveRandom\LibDNS\Messages\Message;

final class EncodingContext
{
    private $pendingRecordHeaderData = null;
    private $pendingData = '';
    private $limitTo512Bytes;

    /** @var string */
    public $data = '';

    /** @var int */
    public $offset = Message::HEADER_SIZE;

    /** @var int[] */
    public $labelIndexes = [];

    /** @var bool */
    public $compress;

    /** @var bool */
    public $isTruncated = false;

    public function __construct(int $options)
    {
        $this->compress = !($options & EncodingOptions::NO_COMPRESSION);
        $this->limitTo512Bytes = !($options & EncodingOptions::FORMAT_TCP);
    }

    public function beginRecordData()
    {
        $this->pendingRecordHeaderData = $this->pendingData;
        $this->pendingData = '';
        $this->offset += 2;
    }

    public function appendData(string $data)
    {
        $this->pendingData .= $data;
        $this->offset += \strlen($data);
    }

    public function commitPendingData()
    {
        if ($this->pendingRecordHeaderData !== null) {
            $this->data .= $this->pendingRecordHeaderData . \pack('n', \strlen($this->pendingData));
        }

        $this->data .= $this->pendingData;

        $this->pendingRecordHeaderData = null;
        $this->pendingData = '';
    }

    public function isDataLengthExceeded(): bool
    {
        return ($this->limitTo512Bytes && $this->offset > 512) || $this->offset > 65535;
    }
}
