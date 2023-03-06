<?php

declare(strict_types=1);

namespace Tests\VatsimClient;

use CuyZ\Valinor\Mapper\Source\Source;
use CuyZ\Valinor\MapperBuilder;
use DateTime;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use stdClass;
use Throwable;
use VatRadar\VatsimClient\Exception\ObjectMappingException;
use VatRadar\VatsimClient\Mapper;

#[CoversClass(Mapper::class)]
#[UsesClass(ObjectMappingException::class)]
class MapperTest extends MockeryTestCase
{
    #[DataProvider('makeIterableDataProvider')]
    public function testMakeIterable(string $json, mixed $expected): void
    {
        $mapper = new Mapper(new MapperBuilder(), stdClass::class);
        $result = $mapper->makeIterable($json);
        static::assertEquals($expected, $result);
    }

    public static function makeIterableDataProvider(): array
    {
        return [
            ['json' => '{"name": "John Doe", "age": 30}', 'expected' => Source::json('{"name": "John Doe", "age": 30}')->camelCaseKeys()],
            ['json' => '{"name": "Jane Doe", "age": 25}', 'expected' => Source::json('{"name": "Jane Doe", "age": 25}')->camelCaseKeys()],
            ['json' => '{"name": "John Smith", "age": 35}', 'expected' => Source::json('{"name": "John Smith", "age": 35}')->camelCaseKeys()],
            ['json' => '{"name": "Jane Smith", "age": 40}', 'expected' => Source::json('{"name": "Jane Smith", "age": 40}')->camelCaseKeys()],
        ];
    }

    #[DataProvider('mapDataProvider')]
    public function testMap(array $source, mixed $expected): void
    {
        $mapper = new Mapper(new MapperBuilder(), MapperTestObject::class);
        try {
            $result = $mapper->map($source);
            static::assertEquals($expected, $result);
        } catch (Throwable $e) {
            static::assertInstanceOf($expected, $e);
        }
    }

    public static function mapDataProvider(): array
    {
        return [
            [['name' => 'John Doe', 'age' => 30, 'dateOfBirth' => '1990-01-01T00:00:00+00:00'], new MapperTestObject('John Doe', 30, new DateTime('1990-01-01T00:00:00+00:00'))],
            [['name' => 'Jane Doe', 'age' => 25, 'dateOfBirth' => '1995-01-01T00:00:00+00:00'], new MapperTestObject('Jane Doe', 25, new DateTime('1995-01-01T00:00:00+00:00'))],
            [['name' => 'John Smith', 'age' => 35, 'dateOfBirth' => '1985-01-01T00:00:00+00:00'], new MapperTestObject('John Smith', 35, new DateTime('1985-01-01T00:00:00+00:00'))],
            [['name' => 'Jane Smith', 'age' => 40, 'dateOfBirth' => '1980-01-01T00:00:00+00:00'], new MapperTestObject('Jane Smith', 40, new DateTime('1980-01-01T00:00:00+00:00'))],
            [['name' => 42, 'age' => 'nunya', 'dateOfBirth' => ['whoops']], ObjectMappingException::class],
            [['name' => 'Robert Bob', 'age' => 37, 'dateOfBirth' => 'this is not a date'], ObjectMappingException::class],
        ];
    }
}

class MapperTestObject
{
    public function __construct(
        public readonly string $name,
        public readonly int $age,
        public readonly DateTime $dateOfBirth
    ) {
    }
}
