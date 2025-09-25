<?php

use Hdaklue\Polish\NumberPolisher;

test('ordinal method returns correct ordinal suffixes', function () {
    expect(NumberPolisher::ordinal(1))
        ->toBe('1st')
        ->and(NumberPolisher::ordinal(2))
        ->toBe('2nd')
        ->and(NumberPolisher::ordinal(3))
        ->toBe('3rd')
        ->and(NumberPolisher::ordinal(4))
        ->toBe('4th')
        ->and(NumberPolisher::ordinal(21))
        ->toBe('21st')
        ->and(NumberPolisher::ordinal(22))
        ->toBe('22nd')
        ->and(NumberPolisher::ordinal(23))
        ->toBe('23rd')
        ->and(NumberPolisher::ordinal(24))
        ->toBe('24th');
});

test('ordinal method handles special cases (11th, 12th, 13th)', function () {
    expect(NumberPolisher::ordinal(11))
        ->toBe('11th')
        ->and(NumberPolisher::ordinal(12))
        ->toBe('12th')
        ->and(NumberPolisher::ordinal(13))
        ->toBe('13th')
        ->and(NumberPolisher::ordinal(111))
        ->toBe('111th')
        ->and(NumberPolisher::ordinal(112))
        ->toBe('112th')
        ->and(NumberPolisher::ordinal(113))
        ->toBe('113th');
});

test('ordinal method handles negative numbers', function () {
    expect(NumberPolisher::ordinal(-1))
        ->toBe('-1st')
        ->and(NumberPolisher::ordinal(-21))
        ->toBe('-21st');
});

test('range method creates proper range format', function () {
    expect(NumberPolisher::range(10, 20))
        ->toBe('10–20')
        ->and(NumberPolisher::range(1, 100))
        ->toBe('1–100');
});

test('range method with custom separator', function () {
    expect(NumberPolisher::range(10, 20, ' to '))
        ->toBe('10 to 20')
        ->and(NumberPolisher::range(1, 5, '-'))
        ->toBe('1-5');
});

test('roman method converts numbers to Roman numerals', function () {
    expect(NumberPolisher::roman(1))
        ->toBe('I')
        ->and(NumberPolisher::roman(4))
        ->toBe('IV')
        ->and(NumberPolisher::roman(5))
        ->toBe('V')
        ->and(NumberPolisher::roman(9))
        ->toBe('IX')
        ->and(NumberPolisher::roman(10))
        ->toBe('X')
        ->and(NumberPolisher::roman(42))
        ->toBe('XLII')
        ->and(NumberPolisher::roman(399))
        ->toBe('CCCXCIX');
});

test('roman method handles edge cases', function () {
    expect(NumberPolisher::roman(0))
        ->toBe('0')
        ->and(NumberPolisher::roman(-5))
        ->toBe('-5')
        ->and(NumberPolisher::roman(400))
        ->toBe('400');
});

test('score method creates score format', function () {
    expect(NumberPolisher::score(85, 100))
        ->toBe('85/100')
        ->and(NumberPolisher::score(7, 10))
        ->toBe('7/10')
        ->and(NumberPolisher::score(0, 5))
        ->toBe('0/5');
});

test('rating method creates star ratings', function () {
    expect(NumberPolisher::rating(5.0))
        ->toBe('★★★★★')
        ->and(NumberPolisher::rating(4.5))
        ->toBe('★★★★☆')
        ->and(NumberPolisher::rating(3.0))
        ->toBe('★★★☆☆')
        ->and(NumberPolisher::rating(0.0))
        ->toBe('☆☆☆☆☆');
});

test('rating method with custom max stars', function () {
    expect(NumberPolisher::rating(7.5, 10))
        ->toBe('★★★★★★★☆☆☆')
        ->and(NumberPolisher::rating(2.0, 3))
        ->toBe('★★☆');
});

test('NumberPolisher extends BasePolisher', function () {
    expect(NumberPolisher::class)
        ->toHaveStaticMethod('ordinal')
        ->toHaveStaticMethod('range')
        ->toHaveStaticMethod('roman')
        ->toHaveStaticMethod('score')
        ->toHaveStaticMethod('rating');
});