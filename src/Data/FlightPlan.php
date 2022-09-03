<?php
/*
 * This file is part of a VATRadar package.
 *
 * Copyright (c) 2022 VATRadar <dev@vatradar.net>
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Vatradar\Vatsimclient\Data;

final class FlightPlan
{
    public function __construct(
        public readonly string $flightRules,
        public readonly string $aircraft,
        public readonly string $aircraftFaa,
        public readonly string $aircraftShort,
        public readonly string $departure,
        public readonly string $arrival,
        public readonly string|null $alternate,
        public readonly string $cruiseTas,
        public readonly string $altitude,
        public readonly string $deptime,
        public readonly string $enrouteTime,
        public readonly string $fuelTime,
        public readonly string|null $remarks,
        public readonly string|null $route,
        public readonly int $revisionId,
        public readonly string $assignedTransponder,
    ) {}
}
