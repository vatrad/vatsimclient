<?php declare(strict_types=1);
/*
 * This file is part of a VATRadar package.
 *
 * Copyright (c) 2022 VATRadar <dev@vatradar.net>
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Vatradar\Vatsimclient\Filter;


use Aura\Filter\SubjectFilter;

class PilotFilter extends SubjectFilter
{
    /**
     * @return void
     */
    protected function init(): void
    {
        // ensure cid is an integer
        $this->sanitize('cid')->to('int');

        // ensure name is set and is only alphanumeric
        // ensure length is less than 50 (arbitrary constraint)

        $this->sanitize('name')
            ->toBlankOr('string')
            ->useBlankValue(' ');

        $this->sanitize('name')
            ->to('strlenMax', 50);


        // ensure callsign is alphanumeric
        // and all uppercase
        // and not longer than 10 chars
        $this->sanitize('callsign')
            ->to('strlenMax', 10);

        $this->sanitize('callsign')
            ->to('alnum');


        // ensure rating is an integer
        $this->sanitize('pilot_rating')
            ->to('int');

        // ensure latitude and longitude are float
        $this->sanitize('latitude')
            ->to('float');
        $this->sanitize('latitude')
            ->to('between', -90, 90);

        $this->sanitize('longitude')
            ->to('float');
        $this->sanitize('longitude')
            ->to('between', -180, 180);

        // ensure altitude is an integer
        $this->sanitize('altitude')->to('int');

        // ensure ground speed is an integer
        $this->sanitize('groundspeed')->to('int');

        // ensure transponder is an integer
        $this->sanitize('transponder')->to('int');

        // ensure heading is an integer
        $this->sanitize('heading')->to('int');

        // ensure qnh are floats and ints respectively
        $this->sanitize('qnh_i_hg')->to('float');
        $this->sanitize('qnh_mb')->to('int');
    }
}
