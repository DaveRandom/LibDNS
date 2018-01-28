<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\HostsFile;

use DaveRandom\Network\IPAddress;

final class HostsFile
{
    private $map;

    public function __construct(array $map)
    {
        $this->map = $map;
    }

    public function containsName($name, int $family = \STREAM_PF_INET): bool
    {
        return isset($this->map[(string)$name][$family]);
    }

    public function getAddressForName($name, int $family = \STREAM_PF_INET): IPAddress
    {
        if (!isset($this->map[$name = (string)$name][$family])) {
            throw new \OutOfBoundsException("No record defined for name {$name} in address family {$family}");
        }

        return $this->map[$name][$family];
    }

    public function toArray(): array
    {
        return $this->map;
    }
}
