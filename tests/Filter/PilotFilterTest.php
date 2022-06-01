<?php
/*
 * This file is part of a VATRadar package.
 *
 * Copyright (c) 2022 VATRadar <dev@vatradar.net>
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Filter;

use Aura\Filter\Exception\FilterFailed;
use Vatradar\Vatsimclient\Filter\PilotFilter;
use PHPUnit\Framework\TestCase;
use Aura\Filter\FilterFactory;

class PilotFilterTest extends TestCase
{
    private PilotFilter $filter;

    protected function setUp(): void
    {
        $factory = new FilterFactory();
        $this->filter = $factory->newSubjectFilter(PilotFilter::class);
    }

    /**
     * @dataProvider cidProvider
     */
    public function testCid(mixed $input, mixed $expected): void
    {
        $field = 'cid';

        $td = $this->applyFilter($field, $input);

        $this->assertSame($expected, $td[$field]);
    }

    /**
     * @dataProvider nameProvider
     */
    public function testName(mixed $input, mixed $expected): void
    {
        $field = 'name';

        $td = $this->applyFilter($field, $input);

        $this->assertSame($expected, $td[$field]);
    }

    /**
     * @dataProvider callsignProvider
     */
    public function testCallsign(mixed $input, mixed $expected): void
    {
        $field = 'callsign';

        $td = $this->applyFilter($field, $input);

        $this->assertSame($expected, $td[$field]);
    }

    /**
     * @dataProvider ratingProvider
     */
    public function testPilotRating(mixed $input, mixed $expected): void
    {
        $field = 'pilot_rating';

        $td = $this->applyFilter($field, $input);

        $this->assertSame($expected, $td[$field]);

    }

    /**
     * @dataProvider latitudeProvider
     */
    public function testLatitude(mixed $input, mixed $expected): void
    {
        $field = 'latitude';

        $td = $this->applyFilter($field, $input);

        $this->assertSame($expected, $td[$field]);

    }

    /**
     * @dataProvider longitudeProvider
     */
    public function testLongitude(mixed $input, mixed $expected): void
    {
        $field = 'longitude';

        $td = $this->applyFilter($field, $input);

        $this->assertSame($expected, $td[$field]);

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

    /**
     * @dataProvider groundspeedProvider
     */
    public function testGroundspeed(mixed $input, mixed $expected): void
    {
        $field = 'groundspeed';

        $td = $this->applyFilter($field, $input);

        $this->assertSame($expected, $td[$field]);

    }

    /**
     * @dataProvider headingProvider
     */
    public function testHeading(mixed $input, mixed $expected): void
    {
        $field = 'heading';

        $td = $this->applyFilter($field, $input);

        $this->assertSame($expected, $td[$field]);

    }

    /**
     * @dataProvider transponderProvider
     */
    public function testTransponder(mixed $input, mixed $expected): void
    {
        $field = 'transponder';

        $td = $this->applyFilter($field, $input);

        $this->assertSame($expected, $td[$field]);

    }

    /**
     * @dataProvider qnhHgProvider
     */
    public function testQnhHg(mixed $input, mixed $expected): void
    {
        $field = 'qnh_i_hg';

        $td = $this->applyFilter($field, $input);

        $this->assertSame($expected, $td[$field]);

    }

    /**
     * @dataProvider qnhMbProvider
     */
    public function testQnhMb(mixed $input, mixed $expected): void
    {
        $field = 'qnh_mb';

        $td = $this->applyFilter($field, $input);

        $this->assertSame($expected, $td[$field]);

    }

    /**
     * @return array
     */
    public function cidProvider(): array
    {
        return [
            [123456, 123456],
            ['a123456b', 123456],
            ['notint', 0]
        ];
    }

    /**
     * @return array
     */
    public function nameProvider(): array
    {
        return [
            ['Test User', 'Test User'],
            ['!Test @$Dude', '!Test @$Dude'],
            ['This is a string longer than fifty characters and will be truncated', 'This is a string longer than fifty characters and ']
        ];
    }

    /**
     * @return array
     */
    public function callsignProvider(): array
    {
        return [
            ['N182BU', 'N182BU'],
            ['N!!42%7B', 'N427B'],
            ['LONGCALLSIGN10', 'LONGCALLSI' ],
            ['!@ #$ %^', '']
        ];
    }

    /**
     * @return array
     */
    public function ratingProvider(): array
    {
        return [
            [0, 0],
            [50, 50],
            ['string', 0]
        ];
    }

    /**
     * @return array
     */
    public function latitudeProvider(): array
    {
        return [
            [0, 0.0],
            [-120, -90],
            [120, 90],
            [23.83, 23.83],
            [-20.84, -20.84]
        ];
    }

    /**
     * @return array
     */
    public function longitudeProvider(): array
    {
        return [
            [0, 0.0],
            [-190, -180],
            [190, 180],
            [39.29, 39.29],
            [-59.59, -59.59]
        ];
    }

    /**
     * @return array
     */
    public function altitudeProvider(): array
    {
        return [
            [0, 0],
            [-200, -200],
            [35553, 35553],
            ['250', 250],
            ['string', 0]
        ];
    }

    /**
     * @return array
     */
    public function groundspeedProvider(): array
    {
        return [
            [0, 0],
            [500, 500],
            ['350', 350],
            ['string', 0]
        ];
    }

    /**
     * @return array
     */
    public function headingProvider(): array
    {
        return [
            [0, 0]
        ];
    }

    /**
     * @return array
     */
    public function transponderProvider(): array
    {
        return [
            ['0000', 0],
            [1234, 1234],
            ['1234', 1234],
            ['7700', 7700],
            [6501, 6501]
        ];
    }

    /**
     * @return array
     */
    public function qnhHgProvider(): array
    {
        return [
            [29.92, 29.92],
            [30.12, 30.12],
            ['29.84', 29.84]
        ];
    }

    /**
     * @return array
     */
    public function qnhMbProvider(): array
    {
        return [
            [1013, 1013],
            [1022, 1022],
            ['1012', 1012]

        ];
    }

    /**
     * @param string $field
     * @param mixed $input
     * @return array
     * @throws FilterFailed
     */
    public function applyFilter(string $field, mixed $input): array
    {
        $td = $this->protoPilot();
        $td[$field] = $input;

        $this->filter->assert($td);

        return $td;
    }

    /**
     * @return array
     */
    public function protoPilot(): array
    {
        return [
            'cid' => 1234567,
            'name' => "Test User KATL",
            'callsign' => 'N42NJ',
            'pilot_rating' => 0,
            'latitude' => 33.91468,
            'longitude' => -84.51446,
            'altitude' => 12200,
            'groundspeed' => 350,
            'transponder' => '2000',
            'heading' => 340,
            'qnh_i_hg' => 30.3,
            'qnh_mb' => 1026,
        ];
    }

}
