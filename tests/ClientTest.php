<?php

declare(strict_types=1);

namespace Tests\VatsimClient;

use JsonException;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Throwable;
use VatRadar\VatsimClient\Client as VatsimClient;
use VatRadar\VatsimClient\DataFetcher;
use VatRadar\VatsimClient\Exception\HttpException;
use VatRadar\VatsimClient\IterableSanitizer;
use VatRadar\VatsimClient\Mapper;

#[CoversClass(VatsimClient::class)]
class ClientTest extends MockeryTestCase
{
    /**
     * @dataProvider retrieveDataProvider
     */
    public function testRetrieve($dataFetcher, $iterableSanitizer, $mapper, mixed $expected): void
    {
        $client = new VatsimClient($dataFetcher, $iterableSanitizer, $mapper);
        try {
            static::assertEquals($expected, $client->retrieve());
        } catch (Throwable $e) {
            static::assertInstanceOf($expected, $e);
        }
    }

    public static function retrieveDataProvider(): array
    {
        $dataFetcher1 = Mockery::mock(DataFetcher::class);
        $dataFetcher1->allows('fetch')->andReturns('{"foo": "bar"}');
        $dataFetcher2 = Mockery::mock(DataFetcher::class);
        $dataFetcher2->allows('fetch')->andReturns('{"foo": "baz"}');
        $dataFetcher3 = Mockery::mock(DataFetcher::class);
        $dataFetcher3->allows('fetch')->andThrows(new HttpException());
        $dataFetcher4 = Mockery::mock(DataFetcher::class);
        $dataFetcher4->allows('fetch')->andThrows(new JsonException());
        $iterableSanitizer1 = Mockery::mock(IterableSanitizer::class);
        $iterableSanitizer1->allows('clean')->andReturns(['foo' => 'bar']);
        $iterableSanitizer2 = Mockery::mock(IterableSanitizer::class);
        $iterableSanitizer2->allows('clean')->andReturns(['foo' => 'baz']);
        $mapper1 = Mockery::mock(Mapper::class);
        $mapper1->allows('makeIterable')->andReturns(['foo' => 'bar']);
        $mapper1->allows('map')->andReturns((object) ['foo' => 'bar']);
        $mapper2 = Mockery::mock(Mapper::class);
        $mapper2->allows('makeIterable')->andReturns(['foo' => 'baz']);
        $mapper2->allows('map')->andReturns((object) ['foo' => 'baz']);
        return [
            [$dataFetcher1, $iterableSanitizer1, $mapper1, (object) ['foo' => 'bar']],
            [$dataFetcher2, $iterableSanitizer2, $mapper2, (object) ['foo' => 'baz']],
            [$dataFetcher3, $iterableSanitizer1, $mapper1, HttpException::class],
            [$dataFetcher4, $iterableSanitizer2, $mapper2, JsonException::class],
        ];
    }
}
