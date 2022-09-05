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

final class General
{
    public function __construct(
        public readonly int $version,
        public readonly int $reload,
        public readonly string $update,
        public readonly DateTimeImmutable $updateTimestamp,
        public readonly int $connectedClients,
        public readonly int $uniqueUsers,

    ) {}
}
