<?php

namespace Hdaklue\Polish\Tests\Fixtures;

use Hdaklue\Polish\BasePolisher;

class TestPolisher extends BasePolisher
{
    public static function short(string $value): string
    {
        return substr($value, -6);
    }

    public static function formatted(string $value): string
    {
        return 'v-' . static::short($value);
    }

    public static function uppercase(string $value): string
    {
        return strtoupper($value);
    }

    public static function multipleArgs(string $prefix, string $value, string $suffix = ''): string
    {
        return $prefix . $value . $suffix;
    }

    public static function withSpecialChars(string $value): string
    {
        return '<script>alert("' . $value . '")</script>';
    }

    public static function returnArray(): array
    {
        return ['key' => 'value', 'nested' => ['item' => 'data']];
    }

    public static function returnNull(): ?string
    {
        return null;
    }

    public static function throwException(): never
    {
        throw new \RuntimeException('Test exception message');
    }
}