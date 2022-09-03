<?php
/*
 * This file is part of a VATRadar package.
 *
 * Copyright (c) 2022 VATRadar <dev@vatradar.net>
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Vatradar\Vatsimclient\Data;

final class PilotRating
{
    public function __construct(
        public readonly int $id,
        public readonly string $shortName,
        public readonly string $longName,
    ) {}
}
