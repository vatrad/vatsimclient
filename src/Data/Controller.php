<?php
/*
 * This file is part of a VATRadar package.
 *
 * Copyright (c) 2022 VATRadar <dev@vatradar.net>
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Vatradar\Vatsimclient\Data;

use DateTimeImmutable;

final class Controller
{
    public function __construct(
        public readonly int $cid,
        public readonly string|null $name,
        public readonly string $callsign,
        public readonly string|null $frequency,
        public readonly int $facility,
        public readonly int $rating,
        public readonly string $server,
        public readonly int $visualRange,
        /** @var null|string[] */
        public readonly array|null $textAtis,
        public readonly DateTimeImmutable $lastUpdated,
        public readonly DateTimeImmutable $logonTime,

    ) {}
}
