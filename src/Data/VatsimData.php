<?php
/*
 * This file is part of a VATRadar package.
 *
 * Copyright (c) 2022 VATRadar <dev@vatradar.net>
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Vatradar\Vatsimclient\Data;


final class VatsimData
{
    public function __construct(
        public readonly General $general,
        /** @var Pilot[] */
        public readonly array $pilots,
        /** @var Controller[]  */
        public readonly array $controllers,
        /** @var Atis[] */
        public readonly array $atis,
        /** @var Server[]  */
        public readonly array $servers,
        /** @var Prefile[] */
        public readonly array $prefiles,
        /** @var Facility[]  */
        public readonly array $facilities,
        /** @var Rating[] */
        public readonly array $ratings,
        /** @var PilotRating[] */
        public readonly array $pilotRatings,
    ) {}
}
