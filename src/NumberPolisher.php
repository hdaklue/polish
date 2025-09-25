<?php

namespace Hdaklue\Polish;

use Hdaklue\Polish\BasePolisher;

class NumberPolisher extends BasePolisher
{
    public static function ordinal(int $value): string
    {
        $abs = abs($value);
        $lastTwoDigits = $abs % 100;
        $lastDigit = $abs % 10;

        if ($lastTwoDigits >= 11 && $lastTwoDigits <= 13) {
            $suffix = 'th';
        } else {
            $suffix = match ($lastDigit) {
                1 => 'st',
                2 => 'nd',
                3 => 'rd',
                default => 'th',
            };
        }

        return $value . $suffix;
    }

    public static function range(int $min, int $max, string $separator = '–'): string
    {
        return $min . $separator . $max;
    }

    public static function roman(int $value): string
    {
        if ($value < 1 || $value > 399) {
            return (string) $value;
        }

        $romanNumerals = [
            1000 => 'M',
            900 => 'CM',
            500 => 'D',
            400 => 'CD',
            100 => 'C',
            90 => 'XC',
            50 => 'L',
            40 => 'XL',
            10 => 'X',
            9 => 'IX',
            5 => 'V',
            4 => 'IV',
            1 => 'I',
        ];

        $result = '';
        foreach ($romanNumerals as $decimal => $numeral) {
            while ($value >= $decimal) {
                $result .= $numeral;
                $value -= $decimal;
            }
        }

        return $result;
    }

    public static function score(int $current, int $total): string
    {
        return $current . '/' . $total;
    }

    public static function rating(float $value, int $max = 5): string
    {
        $fullCount = floor($value);
        $hasHalf = ($value - $fullCount) >= 0.5;
        $emptyCount = $max - $fullCount - ($hasHalf ? 1 : 0);
        $full = str_repeat('★', $fullCount);
        $half = $hasHalf ? '☆' : '';
        $empty = str_repeat('☆', $emptyCount);
        return "$full$half$empty";
    }
}
