<?php

declare(strict_types=1);

namespace VatRadar\VatsimClient;

use function htmlentities;
use function is_iterable;
use function is_string;

class IterableSanitizer
{
    public function clean(iterable $data): iterable
    {
        $result = [];

        foreach ($data as $k => $v) {
            if (is_iterable($v)) {
                $result[$k] = $this->clean($v);
                continue;
            }

            if (is_string($v)) {
                $result[$k] = htmlentities($v);
                continue;
            }

            $result[$k] = $v;
        }
        return $result;
    }
}
