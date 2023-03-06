<?php

declare(strict_types=1);

namespace Tests\VatsimClient;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use VatRadar\VatsimClient\IterableSanitizer;

#[CoversClass(IterableSanitizer::class)]
class IterableSanitizerTest extends TestCase
{
    private IterableSanitizer $iterableSanitizer;

    protected function setUp(): void
    {
        $this->iterableSanitizer = new IterableSanitizer();
    }

    #[DataProvider('cleanDataProvider')]
    public function testClean(array $data, mixed $expected): void
    {
        static::assertEquals($expected, $this->iterableSanitizer->clean($data));
    }

    public static function cleanDataProvider(): array
    {
        return [
            [['string' => '<p>This is a string</p>', 'array' => ['<p>This is a string</p>', '<p>This is another string</p>'], 'int' => 123], ['string' => '&lt;p&gt;This is a string&lt;/p&gt;', 'array' => ['&lt;p&gt;This is a string&lt;/p&gt;', '&lt;p&gt;This is another string&lt;/p&gt;'], 'int' => 123]],
            [['string' => '<p>This is a string</p>', 'array' => ['<p>This is a string</p>', '<p>This is another string</p>'], 'int' => 123, 'float' => 123.45], ['string' => '&lt;p&gt;This is a string&lt;/p&gt;', 'array' => ['&lt;p&gt;This is a string&lt;/p&gt;', '&lt;p&gt;This is another string&lt;/p&gt;'], 'int' => 123, 'float' => 123.45]],
            [['string' => '<p>This is a string</p>', 'array' => ['<p>This is a string</p>', '<p>This is another string</p>'], 'int' => 123, 'float' => 123.45, 'bool' => true], ['string' => '&lt;p&gt;This is a string&lt;/p&gt;', 'array' => ['&lt;p&gt;This is a string&lt;/p&gt;', '&lt;p&gt;This is another string&lt;/p&gt;'], 'int' => 123, 'float' => 123.45, 'bool' => true]],
            [['string' => '<p>This is a string</p>', 'array' => ['<p>This is a string</p>', '<p>This is another string</p>'], 'int' => 123, 'float' => 123.45, 'bool' => true, 'null' => null], ['string' => '&lt;p&gt;This is a string&lt;/p&gt;', 'array' => ['&lt;p&gt;This is a string&lt;/p&gt;', '&lt;p&gt;This is another string&lt;/p&gt;'], 'int' => 123, 'float' => 123.45, 'bool' => true, 'null' => null]],
        ];
    }
}
