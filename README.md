# Polish

**Centralized formatting logic for Laravel applications**

Polish solves the problem of presentation logic scattered across your codebase. Instead of hunting down multiple `substr($value, -6)` calls when you need to change how something is displayed, Polish gives you a single place to define and update your formatting rules.

## The Problem

You need to change how a version ID is displayed — from showing the last 6 characters of a ULID to 7. What should be a 10-second change becomes an annoying hunt across:

- Filament column labels
- Livewire computed properties  
- Blade view templates
- API transformers
- DTOs and other data structures

Sure, you could use Eloquent accessors, but that only works when you have a model instance. What about Livewire components without models? Data before it's persisted? API responses? DTOs?

**The real problem isn't `substr()` — it's presentation logic leaking everywhere when it should live in one place.**

## Table of Contents

- [The Solution](#the-solution)
- [Installation](#installation)
- [Basic Usage](#basic-usage)
- [Built-in Polishers](#built-in-polishers)
- [Use Cases](#use-cases)
- [Framework Integration](#framework-integration)
- [Creating Custom Polishers](#creating-custom-polishers)
- [Advanced Features](#advanced-features)
- [Why Polish?](#why-polish)
- [Requirements](#requirements)
- [License](#license)

## The Solution

Polish provides a dead-simple, static-first approach to centralizing formatting logic. Define your formatting rules once, use them everywhere.

```php
// Before: Scattered across your codebase
substr($ulid, -6)  // In Filament
substr($ulid, -6)  // In Livewire  
substr($ulid, -6)  // In Blade
substr($ulid, -6)  // In API transformer

// After: Centralized with Polish
UlidPolisher::short($ulid)  // Everywhere
```

When you need to change from 6 to 7 characters, you update it in one place and it's reflected everywhere — Filament, Livewire, Blade, API responses, DTOs, anywhere.

## Installation

```bash
composer require hdaklue/polish
```

The package will auto-register with Laravel.

## Basic Usage

### Generating Polishers

Use the artisan command to generate a new polisher:

```bash
php artisan polisher:make Document
# Creates: App\Polishers\DocumentPolisher

php artisan polisher:make User/Profile  
# Creates: App\Polishers\User\ProfilePolisher

php artisan polisher:make Payment/Card
# Creates: App\Polishers\Payment\CardPolisher
```

### Manual Creation

Alternatively, create a polisher by extending the `BasePolisher` class:

```php
<?php

namespace App\Polishers;

use Hdaklue\Polish\BasePolisher;

class UlidPolisher extends BasePolisher
{
    public static function short(string $ulid): string
    {
        return substr($ulid, -7); // Changed from 6 to 7 — everywhere updates
    }
    
    public static function polish(string $ulid): string
    {
        return sprintf('v-%s', static::short($ulid));
    }
}
```

Then use it anywhere in your application:

```php
// In Filament
Tables\Columns\TextColumn::make('version_id')
    ->label('Version')
    ->formatStateUsing(fn ($state) => UlidPolisher::short($state))

// In Livewire
#[Computed]
public function displayVersion(): string
{
    return UlidPolisher::polish($this->version_id);
}

// In Blade
{{ UlidPolisher::short($model->version_id) }}
{{ UlidPolisher::polish($model->version_id) }}

// In API Resources
'version' => UlidPolisher::short($this->version_id),
'formatted_version' => UlidPolisher::polish($this->version_id)

// In DTOs, transformers, anywhere
$dto->version = UlidPolisher::short($rawData['version_id']);
$dto->display_version = UlidPolisher::polish($rawData['version_id']);
```

## Built-in Polishers

Polish comes with two powerful polishers that complement Laravel's built-in formatting:

### NumberPolisher

Advanced number formatting beyond Laravel's `Number` class:

```php
use Hdaklue\Polish\NumberPolisher;

// Ordinal numbers
NumberPolisher::ordinal(1);    // "1st"
NumberPolisher::ordinal(22);   // "22nd" 
NumberPolisher::ordinal(103);  // "103rd"

// Roman numerals (1-399)
NumberPolisher::roman(42);     // "XLII"
NumberPolisher::roman(399);    // "CCCXCIX"
NumberPolisher::roman(400);    // "400" (fallback)

// Number ranges
NumberPolisher::range(10, 20);           // "10–20"
NumberPolisher::range(1, 5, ' to ');     // "1 to 5"

// Score formatting
NumberPolisher::score(85, 100);          // "85/100"
NumberPolisher::score(7, 10);            // "7/10"

// Star ratings
NumberPolisher::rating(4.5);             // "★★★★☆"
NumberPolisher::rating(7.5, 10);         // "★★★★★★★☆☆☆"
```

### StringPolisher

Advanced string operations beyond Laravel's `Str` class:

```php
use Hdaklue\Polish\StringPolisher;

// Smart masking with patterns
StringPolisher::smartMask('john@email.com', 'email');    // "jo****@email.com"
StringPolisher::smartMask('555-123-4567', 'phone');      // "555-***-4567"
StringPolisher::smartMask('4111-1111-1111-1111', 'card'); // "4111-****-****-1111"

// Intelligent excerpts with keyword centering
$text = 'Long article with important keyword somewhere in middle...';
StringPolisher::excerpt($text, 50, 'keyword');           // "...important keyword somewhere..."

// Technical string humanization
StringPolisher::humanize('user_profile_api');            // "User Profile API"
StringPolisher::humanize('payment-gateway-config');      // "Payment Gateway Config"

// Name initials
StringPolisher::initials('John Doe');                    // "JD"
StringPolisher::initials('John Michael Smith', 3);       // "JMS"

// Social media formatting  
StringPolisher::mention('John Doe');                     // "@john-doe"
StringPolisher::hashtag('React Native');                 // "#ReactNative"
```

## Use Cases

### Data Formatting
```php
class CurrencyPolisher extends BasePolisher
{
    public static function money(int $cents): string
    {
        return '$' . number_format($cents / 100, 2);
    }
    
    public static function compact(int $cents): string
    {
        $amount = $cents / 100;
        if ($amount >= 1000000) {
            return '$' . round($amount / 1000000, 1) . 'M';
        }
        if ($amount >= 1000) {
            return '$' . round($amount / 1000, 1) . 'K';
        }
        return static::money($cents);
    }
}
```

### Status and States
```php
class OrderStatusPolisher extends BasePolisher
{
    public static function badge(string $status): string
    {
        return match($status) {
            'pending' => '🟡 Pending',
            'processing' => '🔵 Processing', 
            'shipped' => '🟢 Shipped',
            'delivered' => '✅ Delivered',
            'cancelled' => '🔴 Cancelled',
            default => '❓ Unknown'
        };
    }
    
    public static function color(string $status): string
    {
        return match($status) {
            'pending' => 'warning',
            'processing' => 'info',
            'shipped' => 'success', 
            'delivered' => 'success',
            'cancelled' => 'danger',
            default => 'gray'
        };
    }
}
```

### Date and Time
```php
class DatePolisher extends BasePolisher
{
    public static function relative(\DateTimeInterface $date): string
    {
        return $date->diffForHumans();
    }
    
    public static function short(\DateTimeInterface $date): string
    {
        return $date->format('M j, Y');
    }
    
    public static function time(\DateTimeInterface $date): string
    {
        return $date->format('g:i A');
    }
}
```

## Framework Integration

### Filament
```php
// In table columns
TextColumn::make('amount')
    ->formatStateUsing(fn ($state) => CurrencyPolisher::money($state))

// In forms
Placeholder::make('formatted_total')
    ->content(fn ($record) => CurrencyPolisher::compact($record->total))
```

### Livewire
```php
class OrderComponent extends Component
{
    #[Computed]
    public function statusBadge(): string
    {
        return OrderStatusPolisher::badge($this->order->status);
    }
    
    #[Computed] 
    public function formattedTotal(): string
    {
        return CurrencyPolisher::money($this->order->total);
    }
}
```

### Blade Components
```php
// In your blade components
<x-status-badge :color="OrderStatusPolisher::color($order->status)">
    {{ OrderStatusPolisher::badge($order->status) }}
</x-status-badge>

<span class="font-mono">
    {{ UlidPolisher::short($model->id) }}
</span>
```

### API Resources
```php
class OrderResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => UlidPolisher::short($this->id),
            'status' => OrderStatusPolisher::badge($this->status),
            'total' => CurrencyPolisher::money($this->total),
            'created_at' => DatePolisher::relative($this->created_at),
        ];
    }
}
```

## Creating Custom Polishers

Polish is designed to be extended with your own creative polishers. Here's how to build powerful, reusable formatting logic:

### Business-Specific Polishers

```php
// E-commerce specific formatting
class ProductPolisher extends BasePolisher
{
    public static function sku(string $sku): string
    {
        return strtoupper(substr($sku, 0, 3) . '-' . substr($sku, 3));
    }
    
    public static function availability(int $stock): string
    {
        return match(true) {
            $stock === 0 => '🔴 Out of Stock',
            $stock < 5 => '🟡 Low Stock (' . $stock . ' left)',
            $stock < 20 => '🟢 In Stock (' . $stock . ' available)',
            default => '🟢 In Stock (20+ available)'
        };
    }
    
    public static function badge(float $rating, int $reviews): string
    {
        if ($reviews < 10) return '';
        if ($rating >= 4.5) return '⭐ Bestseller';
        if ($rating >= 4.0) return '👍 Recommended';
        return '';
    }
}
```

### Creative Data Formatting

```php
// Fun and creative formatters
class CreativePolisher extends BasePolisher
{
    public static function progress(int $current, int $total): string
    {
        $percentage = ($current / $total) * 100;
        $filled = floor($percentage / 10);
        return str_repeat('█', $filled) . str_repeat('░', 10 - $filled) . " {$percentage}%";
    }
    
    public static function mood(string $sentiment): string
    {
        return match($sentiment) {
            'positive' => '😊 ' . $sentiment,
            'negative' => '😞 ' . $sentiment,  
            'neutral' => '😐 ' . $sentiment,
            default => '🤷 unknown'
        };
    }
    
    public static function fileIcon(string $extension): string
    {
        return match($extension) {
            'pdf' => '📄',
            'jpg', 'png', 'gif' => '🖼️',
            'mp4', 'mov' => '🎬',
            'mp3', 'wav' => '🎵',
            'zip', 'tar' => '🗜️',
            default => '📁'
        };
    }
}
```

### Integration Polishers

```php
// API and external service formatters  
class IntegrationPolisher extends BasePolisher
{
    public static function githubUrl(string $repo): string
    {
        return "https://github.com/{$repo}";
    }
    
    public static function slackMention(string $userId): string
    {
        return "<@{$userId}>";
    }
    
    public static function discordEmbed(string $title, string $description): array
    {
        return [
            'embeds' => [[
                'title' => $title,
                'description' => $description,
                'color' => 0x00ff00
            ]]
        ];
    }
}
```

### Domain-Specific Logic

Be as creative as your domain requires:

- **Legal**: Case numbers, statute formatting, citation styles
- **Medical**: Dosage formatting, medical record numbers
- **Financial**: Transaction IDs, account masking, audit trails
- **Gaming**: Player stats, achievement badges, leaderboards  
- **Education**: Grade formatting, student IDs, course codes
- **Real Estate**: Property codes, listing formats, MLS numbers

### Best Practices

1. **Keep methods focused** - Each method should do one thing well
2. **Use descriptive names** - `formatCurrency()` is better than `format()`
3. **Handle edge cases** - Always consider null, empty, or invalid inputs
4. **Document your methods** - Add docblocks for complex formatters
5. **Test thoroughly** - Write tests for all your polisher methods

```php
class MyPolisher extends BasePolisher
{
    /**
     * Format a complex business identifier with validation
     */
    public static function businessId(string $id): string
    {
        if (empty($id) || strlen($id) < 6) {
            return $id; // Return as-is for invalid input
        }
        
        return strtoupper(substr($id, 0, 2)) . '-' . 
               substr($id, 2, 4) . '-' . 
               substr($id, 6);
    }
}
```

The goal is to eliminate scattered formatting logic and create a clean, testable, and maintainable system for your presentation layer.

## Advanced Features

### Method Validation
Polish automatically validates that polisher methods exist and throws helpful exceptions:

```php
UlidPolisher::invalidMethod($ulid); 
// Throws: UnsupportedPolisherMethodException: 
// Polisher [App\Polishers\UlidPolisher] does not support static polish method [invalidMethod].
```

### Chainable Operations
Since polishers return formatted data, you can chain operations:

```php
class TextPolisher extends BasePolisher
{
    public static function clean(string $text): string
    {
        return trim(strip_tags($text));
    }
    
    public static function limit(string $text, int $limit = 100): string
    {
        return Str::limit($text, $limit);
    }
}

// Chain operations
$clean = TextPolisher::limit(TextPolisher::clean($userInput), 50);
```

## Why Polish?

- **Single Source of Truth**: Change formatting logic once, see it everywhere
- **Framework Agnostic**: Works in Filament, Livewire, Blade, APIs, DTOs, anywhere
- **Static-First**: No dependency injection, no service location, just clean static calls
- **Zero Configuration**: Extend `BasePolisher`, add methods, start using
- **Laravel Integration**: Auto-discovery, optional config publishing, follows Laravel conventions
- **Developer Experience**: Clear exceptions, predictable API, familiar patterns

## Requirements

- PHP 8.1+
- Laravel 9.0+

## License

MIT