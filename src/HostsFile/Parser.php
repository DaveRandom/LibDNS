<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\HostsFile;

final class Parser
{
    const USE_SYSTEM_LOCALHOST_BEHAVIOUR = 0b1;

    private $flags;

    public function __construct(int $flags = Parser::USE_SYSTEM_LOCALHOST_BEHAVIOUR)
    {
        $this->flags = $flags;
    }

    public function parseString(string $data): HostsFile
    {
        return (new ParsingContext($this->flags))
            ->addData($data)
            ->getResult();
    }

    /**
     * @param resource $stream
     */
    public function parseStream($stream, int $chunkSize = 1024): HostsFile
    {
        if (!\is_resource($stream) || \get_resource_type($stream) !== 'stream') {
            throw new \InvalidArgumentException("Supplied argument is not a valid stream resource");
        }

        $ctx = new ParsingContext($this->flags);

        while ('' !== (string)$chunk = \fread($stream, $chunkSize)) {
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
        return (new ParsingContext($this->flags))
            ->addData($initialData);
    }
}
