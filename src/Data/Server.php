<?php
/*
 * This file is part of a VATRadar package.
 *
 * Copyright (c) 2022 VATRadar <dev@vatradar.net>
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Vatradar\Vatsimclient\Data;

final class Server
{
    public function __construct(
        public readonly string $ident,
        public readonly string $hostnameOrIp,
        public readonly string $location,
        public readonly string $name,
        public readonly int $clientsConnectionAllowed,
        public readonly bool $clientConnectionsAllowed,
        public readonly bool $isSweatbox,
    ) {}
}
