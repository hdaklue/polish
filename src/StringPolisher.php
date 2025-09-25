<?php

namespace Hdaklue\Polish;

use Hdaklue\Polish\BasePolisher;
use Illuminate\Support\Str;

class StringPolisher extends BasePolisher
{
    public static function smartMask(string $value, string $type = 'email'): string
    {
        return match($type) {
            'email' => preg_replace('/(?<=.{2}).(?=.*@)/', '*', $value),
            'phone' => preg_replace('/(?<=.{3})[0-9](?=.{4})/', '*', $value),
            'card' => preg_replace('/(?<=.{4})[0-9](?=.{4})/', '*', $value),
            default => Str::mask($value, '*', 3)
        };
    }

    public static function excerpt(string $value, int $length = 150, string $keyword = null): string
    {
        $value = strip_tags($value);
        
        if ($keyword && Str::contains(Str::lower($value), Str::lower($keyword))) {
            $position = stripos($value, $keyword);
            $start = max(0, $position - $length / 2);
            $excerpt = Str::substr($value, $start, $length);
            
            if ($start > 0) {
                $excerpt = '...' . ltrim($excerpt, '.');
            }
            if (strlen($value) > $start + $length) {
                $excerpt = rtrim($excerpt, '.') . '...';
            }
            
            return trim($excerpt);
        }
        
        return Str::limit($value, $length);
    }

    public static function humanize(string $value): string
    {
        $value = Str::replace(['_', '-', '.'], ' ', $value);
        $value = preg_replace('/([a-z])([A-Z])/', '$1 $2', $value);
        $value = preg_replace('/\bv(\d+)\b/i', 'V$1', $value);
        $value = Str::title($value);
        $value = preg_replace_callback('/\b(Api|Url|Id|Uuid|Json|Xml|Html|Css|Js|Php)\b/', fn($match) => Str::upper($match[0]), $value);
        
        return $value;
    }

    public static function initials(string $value, int $limit = 2): string
    {
        $words = array_filter(array_map('trim', explode(' ', trim($value))), fn($word) => !empty($word));
        $words = array_values($words); // Re-index array
        $initials = '';
        
        for ($i = 0; $i < min(count($words), $limit); $i++) {
            $initials .= Str::upper(Str::substr($words[$i], 0, 1));
        }
        
        return $initials;
    }

    public static function mention(string $value): string
    {
        return '@' . Str::slug($value);
    }

    public static function hashtag(string $value): string
    {
        $cleaned = preg_replace('/[^a-zA-Z0-9\s]/', '', $value);
        return '#' . Str::studly(trim($cleaned));
    }
}