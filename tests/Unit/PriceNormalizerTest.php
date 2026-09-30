<?php

namespace Tests\Unit;

use App\Support\PriceNormalizer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PriceNormalizerTest extends TestCase
{
    public static function validFormats(): array
    {
        return [
            'plain integer' => ['1200', '1200'],
            'dot decimal' => ['1200.50', '1200.50'],
            'comma decimal' => ['1200,50', '1200.50'],
            'european full' => ['1.200,50', '1200.50'],
            'spaced thousands' => ['1 200,50', '1200.50'],
            'dot thousands only' => ['1.200', '1200'],
            'zero' => ['0', '0'],
            'surrounding spaces' => ['  1500  ', '1500'],
            'currency symbol stripped' => ['1.299,90 €', '1299.90'],
        ];
    }

    #[DataProvider('validFormats')]
    public function test_it_normalizes_valid_formats(string $input, string $expected): void
    {
        $this->assertSame($expected, PriceNormalizer::normalize($input));
    }

    public static function invalidFormats(): array
    {
        return [
            'empty' => [''],
            'blank' => ['   '],
            'letters' => ['abc'],
            'multiple dots' => ['12.34.56'],
            'multiple commas' => ['1,2,3'],
        ];
    }

    #[DataProvider('invalidFormats')]
    public function test_it_rejects_invalid_formats(string $input): void
    {
        $this->assertNull(PriceNormalizer::normalize($input));
    }

    public function test_to_float_helper(): void
    {
        $this->assertSame(1200.5, PriceNormalizer::toFloat('1.200,50'));
        $this->assertNull(PriceNormalizer::toFloat('abc'));
    }
}
