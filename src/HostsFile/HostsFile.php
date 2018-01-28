<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\HostsFile;

use DaveRandom\Network\DomainName;
use DaveRandom\Network\IPAddress;

final class HostsFile
{
    private $map;

    public function __construct(array $map)
    {
        $this->map = $map;
    }

    /**
     * @param string|DomainName $name
     */
    public function containsName($name, int $family = \STREAM_PF_INET): bool
    {
        if (!$name instanceof DomainName) {
            try {
                $name = DomainName::createFromString((string)$name);
            } catch (\InvalidArgumentException $e) {
                return false;
            }
        }

        return isset($this->map[(string)$name][$family]);
    }

    public function getAddressForName($name, int $family = \STREAM_PF_INET): IPAddress
    {
        if (!$name instanceof DomainName) {
            try {
                $name = (string)DomainName::createFromString((string)$name);
            } catch (\InvalidArgumentException $e) {
                $name = null;
            }
        }

        if (!isset($name, $this->map[(string)$name][$family])) {
            throw new \OutOfBoundsException("No record defined for name {$name} in address family {$family}");
        }

        return $this->map[(string)$name][$family];
    }

    public function toArray(): array
    {
        return $this->map;
    }
}
