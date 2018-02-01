<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Protocol\Encoding;

use DaveRandom\LibDNS\Protocol\Messages\Message;
use DaveRandom\LibDNS\Records\QuestionRecord;
use DaveRandom\LibDNS\Records\ResourceRecord;

final class Encoder
{
    private $resourceDataEncoder;

    private function encodeHeader(Context $ctx, Message $message, int $qdCount, int $anCount, int $nsCount, int $arCount): string
    {
        return \pack(
            'n6',
            $message->getId(),
            $message->getFlags() | ($message->getOpCode() << 11) | ($ctx->isTruncated << 9) | $message->getResponseCode(),
            $qdCount,
            $anCount,
            $nsCount,
            $arCount
        );
    }

    private function encodeQuestionRecord(Context $ctx, QuestionRecord $record): bool
    {
        \DaveRandom\LibDNS\encode_domain_name($ctx, $record->getName());
        $ctx->appendData(\pack('n2', $record->getType(), $record->getClass()));

        if ($ctx->isDataLengthExceeded()) {
            $ctx->isTruncated = true;
            return false;
        }

        $ctx->commitPendingData();
        return true;
    }

    private function encodeResourceRecord(Context $ctx, ResourceRecord $record): bool
    {
        \DaveRandom\LibDNS\encode_domain_name($ctx, $record->getName());
        $ctx->appendData(\pack('n2N', $record->getType(), $record->getClass(), $record->getTTL()));

        $ctx->beginRecordData();
        $this->resourceDataEncoder->encode($ctx, $record->getType(), $record->getData());

        if ($ctx->isDataLengthExceeded()) {
            $ctx->isTruncated = true;
            return false;
        }

        $ctx->commitPendingData();
        return true;
    }

    public function __construct(ResourceDataEncoder $resourceDataEncoder = null)
    {
        $this->resourceDataEncoder = $resourceDataEncoder ?? new ResourceDataEncoder();
    }

    /**
     * Encode a Message to raw network data
     *
     * @param Message $message  The Message to encode
     * @param bool $compress Enable message compression
     * @return string
     */
    public function encode(Message $message, int $options = 0): string
    {
        $ctx = new Context($options);
        $qdCount = $anCount = $nsCount = $arCount = 0;

        foreach ($message->getQuestionRecords() as $record) {
            if (!$this->encodeQuestionRecord($ctx, $record)) {
                goto done;
            }

            $qdCount++;
        }

        foreach ($message->getAnswerRecords() as $record) {
            if (!$this->encodeResourceRecord($ctx, $record)) {
                goto done;
            }

            $anCount++;
        }

        foreach ($message->getAuthorityRecords() as $record) {
            if (!$this->encodeResourceRecord($ctx, $record)) {
                goto done;
            }

            $nsCount++;
        }

        foreach ($message->getAdditionalRecords() as $record) {
            if (!$this->encodeResourceRecord($ctx, $record)) {
                goto done;
            }

            $arCount++;
        }

        done:
        $packet = $this->encodeHeader($ctx, $message, $qdCount, $anCount, $nsCount, $arCount) . $ctx->data;

        if (!($options & EncodingOptions::FORMAT_TCP)) {
            \assert(
                \strlen($packet) <= 512,
                new \Error('UDP packet exceeds 512 byte limit: got ' . \strlen($packet) . ' bytes')
            );

            return $packet;
        }

        \assert(
            \strlen($packet) <= 65535,
            new \Error('TCP packet exceeds 65535 byte limit: got ' . \strlen($packet) . ' bytes')
        );

        return \pack('n', \strlen($packet)) . $packet;
    }
}
