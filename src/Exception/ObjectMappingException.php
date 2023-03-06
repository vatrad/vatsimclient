<?php

declare(strict_types=1);

namespace VatRadar\VatsimClient\Exception;

use CuyZ\Valinor\Mapper\Tree\Message\Messages;
use RuntimeException;
use const PHP_EOL;

class ObjectMappingException extends RuntimeException
{
    public function __construct(Messages $messages)
    {
        $mappingErrors = 'Object Mapping Errors: ';

        foreach ($messages as $message) {
            $mappingErrors .= $message . PHP_EOL;
        }

        parent::__construct($mappingErrors);
    }
}
