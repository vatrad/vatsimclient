<?php
/*
 * This file is part of a VATRadar package.
 *
 * Copyright (c) 2022 VATRadar <dev@vatradar.net>
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Filter;

use Aura\Filter\FilterFactory;
use Vatradar\Vatsimclient\Filter\FlightplanFilter;
use PHPUnit\Framework\TestCase;

class FlightplanFilterTest extends TestCase
{
    private FlightplanFilter $filter;


    /**
     * @dataProvider flightRulesProvider
     */
    public function testFlightRules(mixed $input, mixed $expected): void
    {
        $field = 'flight_rules';

        $td = $this->applyFilter($field, $input);

        $this->assertSame($expected, $td[$field]);
    }

    public function flightRulesProvider(): array
    {
        /* Flight rules are expected to come from VATSIM as I for IFR of V for VFR
         * This isn't inputable by the user so we don't strict check for I or VFR
         * However, if it's not set at all, we default to U for undefined.
         */
        return [
            ['i', 'I'],
            ['v', 'V'],
            ['BLAH', 'B'],
            ['', 'U'],
            [12, 'U']

        ];
    }


    /**
     * @dataProvider aircraftProvider
     */
    public function testAircraft(mixed $input, mixed $expected): void
    {
        $field = 'aircraft';

        $td = $this->applyFilter($field, $input);

        $this->assertSame($expected, $td[$field]);
    }

    public function aircraftProvider(): array
    {
        return [
            ['A320/M-VGDW/C', 'A320/M-VGDW/C'],
            ['abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz', 'ABCDEFGHIJKLMNOPQRSTUVWXYZABCDEFGHIJKLMNOPQRSTUVWX'],
            [12, '12'],
            ['12', '12'],
            ['','']
        ];
    }

    /**
     * @dataProvider aircraftFaaProvider
     */
    public function testAircraftFaa(mixed $input, mixed $expected): void
    {
        $field = 'aircraft_faa';

        $td = $this->applyFilter($field, $input);

        $this->assertSame($expected, $td[$field]);
    }

    public function aircraftFaaProvider(): array
    {
        return [
            ['A320/L', 'A320/L'],
            ['a320/l', 'A320/L'],
            ['', ''],
            [12, '12'],
            ['abcdefghijklmnopqrst', 'ABCDEFGHIJ']
        ];
    }

    /**
     * @dataProvider aircraftShortProvider
     */
    public function testAircraftShort(mixed $input, mixed $expected): void
    {
        $field = 'aircraft_short';

        $td = $this->applyFilter($field, $input);

        $this->assertSame($expected, $td[$field]);
    }

    public function aircraftShortProvider(): array
    {
        return [
            ['A320', 'A320'],
            ['a321', 'A321'],
            ['', ''],
            [12, '12'],
            ['abcdefghijkl', 'ABCDE']
        ];
    }

    /**
     * @dataProvider departureProvider
     */
    public function testDeparture(mixed $input, mixed $expected): void
    {
        $field = 'departure';

        $td = $this->applyFilter($field, $input);

        $this->assertSame($expected, $td[$field]);
    }

    public function departureProvider(): array
    {
        return [
            ['KMCO', 'KMCO'],
            ['kclt', 'KCLT'],
            ['5A0', '5A0'],
            [123, '123'],
            ['', 'ZZZZ'],
            ['ABCD1234', 'ABCD123'],
            ['zYxWvUtSrQ', 'ZYXWVUT']
        ];
    }

    /**
     * @dataProvider arrivalProvider
     */
    public function testArrival(mixed $input, mixed $expected): void
    {
        $field = 'arrival';

        $td = $this->applyFilter($field, $input);

        $this->assertSame($expected, $td[$field]);
    }

    public function arrivalProvider(): array
    {
        return [
            ['KMCO', 'KMCO'],
            ['kclt', 'KCLT'],
            ['5A0', '5A0'],
            [123, '123'],
            ['', 'ZZZZ'],
            ['ABCD1234', 'ABCD123'],
            ['zYxWvUtSrQ', 'ZYXWVUT']

        ];
    }

    /**
     * @dataProvider alternateProvider
     */
    public function testAlternate(mixed $input, mixed $expected): void
    {
        $field = 'alternate';

        $td = $this->applyFilter($field, $input);

        $this->assertSame($expected, $td[$field]);
    }

    public function alternateProvider(): array
    {
        return [
            ['KMCO', 'KMCO'],
            ['kclt', 'KCLT'],
            ['5A0', '5A0'],
            [123, '123'],
            ['', 'ZZZZ'],
            ['ABCD1234', 'ABCD123'],
            ['zYxWvUtSrQ', 'ZYXWVUT']
        ];
    }

    /**
     * @dataProvider cruiseProvider
     */
    public function testCruise(mixed $input, mixed $expected): void
    {
        $field = 'cruise_tas';

        $td = $this->applyFilter($field, $input);

        $this->assertSame($expected, $td[$field]);
    }

    public function cruiseProvider(): array
    {
        return [
            ['', 0],
            ['450', 450],
            [300, 300],
            ['abc', 0],
            ['a1b2c3', 123]
        ];
    }

    /**
     * @dataProvider altitudeProvider
     */
    public function testAltitude(mixed $input, mixed $expected): void
    {
        $field = 'altitude';

        $td = $this->applyFilter($field, $input);

        $this->assertSame($expected, $td[$field]);
    }

    public function altitudeProvider(): array
    {
        return [
            [3000, 3000],
            ['28000', 28000],
            ['FL360', 36000],
            ['', 0]
        ];
    }

    /**
     * @dataProvider deptimeProvider
     */
    public function testDeptime(mixed $input, mixed $expected): void
    {
        $field = 'deptime';

        $td = $this->applyFilter($field, $input);

        $this->assertSame($expected, $td[$field]);
    }

    public function deptimeProvider(): array
    {
        return [
            ['0123', '0123'],
            ['01:23', '0123'],
            ['012345', '0123']
        ];
    }

    /**
     * @dataProvider enrouteProvider
     */
    public function testEnroute(mixed $input, mixed $expected): void
    {
        $field = 'enroute_time';

        $td = $this->applyFilter($field, $input);

        $this->assertSame($expected, $td[$field]);
    }

    public function enrouteProvider(): array
    {
        return [
            ['0123', '0123'],
            ['01:23', '0123'],
            ['012345', '0123']
        ];
    }

    /**
     * @dataProvider fuelProvider
     */
    public function testFuel(mixed $input, mixed $expected): void
    {
        $field = 'fuel_time';

        $td = $this->applyFilter($field, $input);

        $this->assertSame($expected, $td[$field]);
    }

    public function fuelProvider(): array
    {
        return [
            ['0123', '0123'],
            ['01:23', '0123'],
            ['012345', '0123']
        ];
    }

    /**
     * @dataProvider remarksProvider
     */
    public function testRemarks(mixed $input, mixed $expected): void
    {
        $field = 'remarks';

        $td = $this->applyFilter($field, $input);

        $this->assertSame($expected, $td[$field]);
    }

    public function remarksProvider(): array
    {
        return [
            ['REMARKS', 'REMARKS'],
            ['these are remarks', 'THESE ARE REMARKS'],
            ['ReMarK 1234', 'REMARK 1234']
        ];
    }

    /**
     * @dataProvider routeProvider
     */
    public function testRoute(mixed $input, mixed $expected): void
    {
        $field = 'route';

        $td = $this->applyFilter($field, $input);

        $this->assertSame($expected, $td[$field]);
    }

    public function routeProvider(): array
    {
        return [
            ['ROUTE', 'ROUTE'],
            ['this is a route', 'THIS IS A ROUTE'],
            ['RoUtE 1234', 'ROUTE 1234']

        ];
    }

    /**
     * @dataProvider revisionProvider
     */
    public function testRevision(mixed $input, mixed $expected): void
    {
        $field = 'revision_id';

        $td = $this->applyFilter($field, $input);

        $this->assertSame($expected, $td[$field]);
    }

    public function revisionProvider(): array
    {
        return [
            [50, 50],
            ['string', 65000],
            [-50, 0],
            [80000, 65000]
        ];
    }

    /**
     * @dataProvider transponderProvider
     */
    public function testTransponder(mixed $input, mixed $expected): void
    {
        $field = 'assigned_transponder';

        $td = $this->applyFilter($field, $input);

        $this->assertSame($expected, $td[$field]);
    }

    public function transponderProvider(): array
    {
        return [
            [1234, '1234'],
            ['5443', '5443'],
            ['12345', '1234'],
            [12345, '1234'],
        ];
    }

    protected function setUp(): void
    {
        $factory = new FilterFactory();
        $this->filter = $factory->newSubjectFilter(FlightplanFilter::class);
    }

    public function applyFilter(string $field, mixed $input): array
    {
        $td = $this->protoPlan();
        $td[$field] = $input;

        $this->filter->assert($td);

        return $td;
    }

    public function protoPlan(): array
    {
        return [
            "flight_rules" => "I",
            "aircraft" => "A320/M-VGDW/C",
            "aircraft_faa" => "A320/L",
            "aircraft_short" => "A320",
            "departure" => "KMCO",
            "arrival" => "KCLT",
            "alternate" => "KGSO",
            "cruise_tas" => "450",
            "altitude" => "37000",
            "deptime" => "0015",
            "enroute_time" => "0120",
            "fuel_time" => "0230",
            "remarks" => "THESE ARE REMARKS /V/",
            "route" => "JEEMY3 PAINN SHRKS Q77 WIGVO PONZE BANKR2",
            "revision_id" => 5,
            "assigned_transponder" => "0716"
        ];
    }
}
