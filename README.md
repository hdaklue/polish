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
- [Use Cases](#use-cases)
- [Framework Integration](#framework-integration)
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

The package will auto-register with Laravel. Optionally publish the config:

```bash
php artisan vendor:publish --tag=polish-config
```

## Basic Usage

Create a polisher by extending the `BasePolisher` class:

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
    
    public static function formatted(string $ulid): string
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
    return UlidPolisher::formatted($this->version_id);
}

// In Blade
{{ UlidPolisher::short($model->version_id) }}

// In API Resources
'version' => UlidPolisher::short($this->version_id)

// In DTOs, transformers, anywhere
$dto->version = UlidPolisher::short($rawData['version_id']);
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