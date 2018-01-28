<?php declare(strict_types=1);

namespace DaveRandom\LibDNS;

interface EncodingContext
{
    function appendData(string $data);
    function getOffset(): int;
    function isCompressionEnabled(): bool;
    function hasIndexForLabel(string $label): bool;
    function getLabelIndex(string $label): int;
    function setLabelIndexAtCurrentOffset(string $label);
}
