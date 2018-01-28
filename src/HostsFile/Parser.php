<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\HostsFile;

final class Parser
{
    public function parseString(string $data): HostsFile
    {
        $ctx = new ParsingContext();
        $ctx->addData($data);

        return $ctx->getResult();
    }

    /**
     * @param resource $stream
     */
    public function parseStream($stream, int $chunkSize = 1024): HostsFile
    {
        if (!\is_resource($stream) || \get_resource_type($stream) !== 'stream') {
            throw new \InvalidArgumentException("Supplied argument is not a valid stream resource");
        }

        $ctx = new ParsingContext();

        while (false !== $chunk = \fread($stream, $chunkSize)) {
            $ctx->addData($chunk);
        }

        return $ctx->getResult();
    }

    public function parseFile(string $filePath): HostsFile
    {
        if (!$fp = \fopen($filePath, 'r')) {
            throw new \InvalidArgumentException("Failed to open {$filePath} for reading");
        }

        return $this->parseStream($fp);
    }

    public function beginParseString(string $initialData = ''): ParsingContext
    {
        $ctx = new ParsingContext();
        $ctx->addData($initialData);

        return $ctx;
    }
}
