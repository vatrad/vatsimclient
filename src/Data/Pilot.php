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

final class Pilot
{
    public function __construct(
        public readonly int $cid,
        public readonly string|null $name,
        public readonly string $callsign,
        public readonly string $server,
        public readonly int $pilotRating,
        public readonly float $latitude,
        public readonly float $longitude,
        public readonly int $altitude,
        public readonly int $groundspeed,
        public readonly string|null $transponder,
        public readonly int $heading,
        public readonly float $qnhIHg,
        public readonly int $qnhMb,
        public readonly FlightPlan|null $flightPlan,
        public readonly DateTimeImmutable $logonTime,
        public readonly DateTimeImmutable $lastUpdated,
    ) {}
}
