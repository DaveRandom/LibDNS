<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Protocol;

interface DecodingContext
{
    function getOffset(): int;
    function getDataLength(): int;
    function advanceOffset(int $length): int;
    function hasData(int $length): bool;
    function getData(int $length): string;
    function unpack(string $spec, int $length): array;
    function hasLabelsAtOffset(int $offset): bool;
    function getLabelsAtOffset(int $offset): array;
    function setLabelsAtOffset(int $offset, array $labels);
}
