<?php declare(strict_types=1);

namespace DaveRandom\LibDNS\Records;

use DaveRandom\Network\DomainName;

final class QuestionRecord extends Record
{
    public function __construct(DomainName $name, int $type, int $class = ResourceClasses::IN)
    {
        parent::__construct($name, $type, $class);
    }
}
