<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Encoding;

final class EncodingContext
{
    /** @var string */
    public $data = '';

    /** @var int[] */
    public $labelIndexes = [];

    /** @var bool */
    public $compress;

    /** @var bool */
    public $limitTo512Bytes;

    /** @var bool */
    public $isTruncated = false;

    /** @var int */
    public $questionRecordCount = 0;

    /** @var int */
    public $answerRecordCount = 0;

    /** @var int */
    public $authorityRecordCount = 0;

    /** @var int */
    public $additionalRecordCount = 0;

    public function __construct(int $options)
    {
        $this->compress = !($options & EncodingOptions::NO_COMPRESSION);
        $this->limitTo512Bytes = !($options & EncodingOptions::FORMAT_TCP);
    }
}
