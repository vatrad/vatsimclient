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
use stdClass;

class FlightplanFilter extends SubjectFilter
{
    /**
     * @return void
     */
    protected function init(): void
    {
        $this->sanitize('flight_rules')->toBlankOr('string');
        $this->sanitize('flight_rules')->toBlankOr('alpha');
        $this->sanitize('flight_rules')->toBlankOr('strlenMax', 1);
        $this->sanitize('flight_rules')->toBlankOr('uppercase')->useBlankValue('U');


        $this->sanitize('aircraft')->toBlankOr('string');
        $this->sanitize('aircraft')->toBlankOr('strlenMax', 50);
        $this->sanitize('aircraft')->toBlankOr('uppercase')->useBlankValue('');

        $this->sanitize('aircraft_faa')->toBlankOr('string');
        $this->sanitize('aircraft_faa')->toBlankOr('strlenMax', 10);
        $this->sanitize('aircraft_faa')->toBlankOr('uppercase')->useBlankValue('');

        $this->sanitize('aircraft_short')->toBlankOr('string');
        $this->sanitize('aircraft_short')->toBlankOr('strlenMax', 5);
        $this->sanitize('aircraft_short')->toBlankOr('uppercase')->useBlankValue('');

        $this->sanitize('departure')->toBlankOr('string');
        $this->sanitize('departure') ->toBlankOr('alnum');
        $this->sanitize('departure')->toBlankOr('strlenMax', 7);
        $this->sanitize('departure')->toBlankOr('uppercase')->useBlankValue('ZZZZ');

        $this->sanitize('arrival')->toBlankOr('string');
        $this->sanitize('arrival') ->toBlankOr('alnum');
        $this->sanitize('arrival')->toBlankOr('strlenMax', 7);
        $this->sanitize('arrival')->toBlankOr('uppercase')->useBlankValue('ZZZZ');

        $this->sanitize('alternate')->toBlankOr('string');
        $this->sanitize('alternate') ->toBlankOr('alnum');
        $this->sanitize('alternate')->toBlankOr('strlenMax', 7);
        $this->sanitize('alternate')->toBlankOr('uppercase')->useBlankValue('ZZZZ');


        $this->sanitize('cruise_tas')
            ->toBlankOr('callback', function($subject, $field) {
                $subject->$field = preg_replace('/\D/i', '', $subject->$field);
                return true;
            })
            ->toBlankOr('int')
            ->useBlankValue(0);

        $this->sanitize('altitude')
            ->toBlankOr('callback', function($subject, $field) {
                $fLevel = false;

                if(is_string($subject->$field)) {
                    if(stripos($subject->$field, 'FL') === 0) {
                        $fLevel = true;
                    }

                    $subject->$field = (int) preg_replace('/\D/i', '', $subject->$field);
                }

                if($fLevel) {
                    $subject->$field *= 100;
                }

                return true;
            })
            ->useBlankValue(0);

        $this->sanitize('deptime')
            ->toBlankOr('callback', function($subject, $field) {
                $subject->$field = preg_replace('/[^\da-z]/i', '', $subject->$field);
                return true;
            });
        $this->sanitize('deptime')->toBlankOr('strlenBetween', 4, 4, '0', STR_PAD_LEFT)
            ->useBlankValue('0000');

        $this->sanitize('enroute_time')
            ->toBlankOr('callback', function($subject, $field) {
                $subject->$field = preg_replace('/[^\da-z]/i', '', $subject->$field);
                return true;
            });
        $this->sanitize('enroute_time')->toBlankOr('strlenBetween', 4, 4, '0', STR_PAD_LEFT)
            ->useBlankValue('0000');

        $this->sanitize('fuel_time')
            ->toBlankOr('callback', function($subject, $field) {
                $subject->$field = preg_replace('/[^\da-z]/i', '', $subject->$field);
                return true;
            });
        $this->sanitize('fuel_time')->toBlankOr('strlenBetween', 4, 4, '0', STR_PAD_LEFT)
            ->useBlankValue('0000');

        $this->sanitize('remarks')
            ->toBlankOr('uppercase')
            ->useBlankValue('');

        $this->sanitize('route')
            ->toBlankOr('uppercase')
            ->useBlankValue('');

        $this->sanitize('revision_id')
            ->to('int')
            ->to('between', 0, 65000);

        $this->sanitize('assigned_transponder')->to('string');
        $this->sanitize('assigned_transponder')->toBlankOr('callback', function($subject, $field) {
            $subject->$field = preg_replace('/[^\da-z]/i', '', $subject->$field);
            return true;
        });
        $this->sanitize('assigned_transponder')->toBlankOr('strlenMax', 4)
            ->useBlankValue('0000');

    }
}
