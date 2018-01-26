<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Encoding;

use DaveRandom\LibDNS\Messages\Message;
use DaveRandom\LibDNS\Records\QuestionRecord;
use DaveRandom\LibDNS\Records\ResourceRecord;
use function DaveRandom\LibDNS\encode_domain_name;

final class Encoder
{
    private $resourceDataEncoder;

    private function encodeHeader(EncodingContext $ctx, Message $message): string
    {
        return \pack(
            'n*',
            $message->getId(),
            $message->getFlags() | ($message->getOpCode() << 11) | ($ctx->isTruncated << 9) | $message->getResponseCode(),
            $ctx->questionRecordCount,
            $ctx->answerRecordCount,
            $ctx->authorityRecordCount,
            $ctx->additionalRecordCount
        );
    }

    private function encodeQuestionRecord(EncodingContext $ctx, QuestionRecord $record): bool
    {
        if ($ctx->isTruncated) {
            return false;
        }

        $name = encode_domain_name($record->getName(), $ctx);

        $newMessageLength = Message::HEADER_SIZE + \strlen($ctx->data) + \strlen($name) + 4;

        if (($ctx->limitTo512Bytes && $newMessageLength > 512) || $newMessageLength > 65535) {
            $ctx->isTruncated = true;
            return false;
        }

        $ctx->data .= \pack('a*n2', $name, $record->getType(), $record->getClass());

        return true;
    }

    private function encodeResourceRecord(EncodingContext $ctx, ResourceRecord $record): bool
    {
        if ($ctx->isTruncated) {
            return false;
        }

        $name = encode_domain_name($record->getName(), $ctx);
        $data = $this->resourceDataEncoder->encode($ctx, $record->getData());

        $newMessageLength = Message::HEADER_SIZE + \strlen($ctx->data) + \strlen($name) + \strlen($data) + 10;

        if (($ctx->limitTo512Bytes && $newMessageLength > 512) || $newMessageLength > 65535) {
            $ctx->isTruncated = true;
            return false;
        }

        $ctx->data .= \pack(
            'a*n2Nna*',
            $name,
            $record->getType(), $record->getClass(), $record->getTTL(),
            \strlen($data), $data
        );

        return true;
    }

    public function __construct()
    {
        $this->resourceDataEncoder = new ResourceDataEncoder();
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
        $ctx = new EncodingContext($options);

        foreach ($message->getQuestionRecords() as $record) {
            if (!$this->encodeQuestionRecord($ctx, $record)) {
                break;
            }

            $ctx->questionRecordCount++;
        }

        foreach ($message->getAnswerRecords() as $record) {
            if (!$this->encodeResourceRecord($ctx, $record)) {
                break;
            }

            $ctx->answerRecordCount++;
        }

        foreach ($message->getAuthorityRecords() as $record) {
            if (!$this->encodeResourceRecord($ctx, $record)) {
                break;
            }

            $ctx->authorityRecordCount++;
        }

        foreach ($message->getAdditionalRecords() as $record) {
            if (!$this->encodeResourceRecord($ctx, $record)) {
                break;
            }

            $ctx->additionalRecordCount++;
        }

        $packet = $this->encodeHeader($ctx, $message) . $ctx->data;

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

        return \pack('a*n', $packet, \strlen($packet));
    }
}
