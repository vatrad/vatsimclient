<?php

declare(strict_types=1);

namespace Tests\VatsimClient;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ServerException;
use InvalidArgumentException;
use JsonException;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Psr\Http\Message\ResponseInterface;
use RangeException;
use VatRadar\VatsimClient\DataFetcher;
use VatRadar\VatsimClient\Exception\HttpException;

use VatRadar\VatsimClient\Exception\MalformedJsonException;

use function json_encode;


use const JSON_THROW_ON_ERROR;

#[CoversClass(DataFetcher::class)]
class DataFetcherTest extends MockeryTestCase
{
    #[DataProvider('fetchDataProvider')]
    public function testFetch(string $bootstrapUri, string $version, string $expected): void
    {
        $httpClient = Mockery::mock(HttpClient::class);
        $response = Mockery::mock('Response , ' . ResponseInterface::class);

        $response->allows('getBody')->andReturns($expected);
        $httpClient->allows('request')->andReturns($response);

        $dataFetcher = new DataFetcher($httpClient, $bootstrapUri, $version);

        $result = $dataFetcher->fetch();
        static::assertEquals($expected, $result);
    }

    public static function fetchDataProvider(): array
    {
        return [
            ['http://example.com/bootstrap.json', 'v3', '{"data": {"v3": ["http://example.com/data1.json"]}}'],
            ['http://example.com/bootstrap.json', 'v4', '{"data": {"v4": ["http://example.com/data3.json"]}}'],
            ['http://example.com/bootstrap.json', 'v5', '{"data": {"v5": ["http://example.com/data5.json"]}}'],
            ['http://example.com/bootstrap.json', 'v6', '{"data": {"v6": ["http://example.com/data7.json"]}}'],
        ];
    }

    #[DataProvider('exceptionsDataProvider')]
    public function testExceptions(ClientInterface $httpClient, mixed $expected): void
    {
        $dataFetcher = new DataFetcher($httpClient, 'https://url.to.nowhere/api');

        $this->expectException($expected);
        $dataFetcher->fetch();
    }

    private static function json(mixed $thing): string
    {
        return json_encode($thing, JSON_THROW_ON_ERROR);
    }

    public static function exceptionsDataProvider(): array
    {
        $h1 = Mockery::mock('GuzzleWuzzle, ' . ClientInterface::class);
        $h1->allows('request')->andThrows(Mockery::mock(ServerException::class));

        $r2 = Mockery::mock('Response , ' . ResponseInterface::class);
        $r2->allows('getBody')->andReturns('[p[[ThiSIsMalForMedJson[[ppp]]]]}}');
        $h2 = Mockery::mock('GuzzFuzz, ' . ClientInterface::class);
        $h2->allows('request')->andReturns($r2);

        $r3 = Mockery::mock('Response , ' . ResponseInterface::class);
        $r3->allows('getBody')->andReturns(static::json(['not' => 'correctJson']));
        $h3 = Mockery::mock('ZzzZZzzZeD, ' . ClientInterface::class);
        $h3->allows('request')->andReturns($r3);

        $r4 = Mockery::mock('Response , ' . ResponseInterface::class);
        $r4->allows('getBody')->andReturns(static::json(['data' => ['v44' => 'thisDontMatter']]));
        $h4 = Mockery::mock('LooneyTunes, ' . ClientInterface::class);
        $h4->allows('request')->andReturns($r4);

        $r5 = Mockery::mock('Response , ' . ResponseInterface::class);
        $r5->allows('getBody')->andReturns(static::json(['data' => ['v3' => []]]));
        $h5 = Mockery::mock('KhazadDum, ' . ClientInterface::class);
        $h5->allows('request')->andReturns($r5);

        return [
            [$h1, HttpException::class],
            [$h2, JsonException::class],
            [$h3, MalformedJsonException::class],
            [$h4, InvalidArgumentException::class],
            [$h5, RangeException::class],
        ];
    }
}
