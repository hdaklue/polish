# Polish

**The Laravel package that transforms enterprise-level formatting chaos into elegant, centralized mastery**

Stop drowning in scattered presentation logic across your production application. Polish gives you one authoritative place to define complex formatting rules that work seamlessly everywhere — from multi-tenant dashboards and white-labeled interfaces to webhook payloads and compliance reports. Finally, a home for the sophisticated formatting logic that actually powers real Laravel applications.

## The Pain Every Laravel Developer Knows Too Well

Picture this soul-crushing scenario: Your product manager walks in Monday morning with "small changes" that trigger a formatting nightmare across your entire application ecosystem.

**Customer tier badges need business rule updates** — Premium customers now get gold badges at $500/month instead of $1000. What seems like a simple threshold change becomes an archaeological dig through:

- **Filament admin dashboards** — Conditional column styles buried in closure hell
- **Multi-tenant interfaces** — Different badge rules per client, duplicated everywhere
- **API endpoints** — Mobile, web, and partner APIs all with different badge logic
- **Email templates** — Welcome emails, invoices, and notifications with inconsistent badge displays
- **PDF reports** — Invoice generators, compliance documents, and executive summaries
- **Webhook payloads** — External integrations expecting specific badge formats
- **Background jobs** — Automated tier assessments running different calculations

**Real-world complexity that keeps you awake at night:**

- Order status workflows with context-sensitive formatting
- Financial data compliance where the same transaction amount displays as currency everywhere
- User permission displays
- Multi-step data transformations

**The cascading nightmare:** When business rules change (and they always do), you're not just updating one formatting method — you're hunting down dozens of scattered implementations across Filament tables, Livewire computed properties, Blade components, API resources, notification templates, PDF generators, webhook transformers, and background job processors.

**The real problem isn't simple string manipulation — it's that your sophisticated business logic formatting has no architectural home, so it metastasizes into an unmaintainable mess of duplicate code, inconsistent implementations, and fragile dependencies scattered across every layer of your production application.**

Your enterprise-grade Laravel application deserves enterprise-grade formatting architecture.

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

## The Solution: Give Your Formatting Logic a Proper Home

Polish provides a beautifully simple, static-first approach that Laravel developers actually *want* to use. Define your formatting rules once in clean "Polisher" classes, then use them everywhere without dependency injection, configuration files, or architectural complexity.

```php
// Before: Scattered, inconsistent, unmaintainable chaos
substr($ulid, -6)           // In Filament column
substr($ulid, -6)           // In Livewire computed property  
substr($ulid, -6)           // In Blade template
substr($ulid, -6)           // In API resource
substr($ulid, -6)           // In notification
substr($ulid, -6)           // In DTO transformation
// ... and 15 other places you'll inevitably forget about

// After: Centralized, consistent, maintainable elegance
UlidPolisher::short($ulid)  // Everywhere, every time, perfectly consistent
```

**The magic moment**: Change from 6 to 7 characters in your polisher class, and instantly watch every Filament column, Livewire component, Blade template, API response, notification, and DTO across your entire application reflect the change. One update, universal consistency, zero hunting.


## Installation: Up and Running in 30 Seconds

Add Polish to your project with a single command:

```bash
composer require hdaklue/polish
```

That's literally it. Polish auto-registers with Laravel using package discovery, requires zero configuration, and you're immediately ready to create your first polisher. No config files to publish, no service providers to register, no setup complexity — just install and start polishing.

## Basic Usage

### Create Your First Polisher in 30 Seconds

Polish includes a delightful Artisan command that generates polisher boilerplate faster than you can think:

```bash
php artisan polisher:make Document
# ✅ Creates: App\Polishers\DocumentPolisher

php artisan polisher:make User/Profile  
# ✅ Creates: App\Polishers\User\ProfilePolisher

php artisan polisher:make Payment/Card
# ✅ Creates: App\Polishers\Payment\CardPolisher
```

The command follows Laravel conventions perfectly — namespaced classes, PSR-4 autoloading, and clean directory structure. Everything you expect, nothing you don't.

### Create Polishers Your Way

Or skip the command and extend `BasePolisher` directly for full control:

```php
<?php

namespace App\Polishers;

use Hdaklue\Polish\BasePolisher;

class UlidPolisher extends BasePolisher
{
    public static function short(string $ulid): string
    {
        return substr($ulid, -7); // Change this once, updates everywhere
    }
    
    public static function display(string $ulid): string
    {
        return sprintf('v-%s', static::short($ulid));
    }
    
    public static function adminFormat(string $ulid): string
    {
        return strtoupper(static::short($ulid));
    }
}
```

### Use It Everywhere (This is Where the Magic Happens)

Now experience the pure satisfaction of watching the same polisher work flawlessly across your entire Laravel ecosystem:

```php
// ✅ In Filament admin panels
Tables\Columns\TextColumn::make('version_id')
    ->label('Version')
    ->formatStateUsing(fn ($state) => UlidPolisher::display($state))

// ✅ In Livewire components  
#[Computed]
public function versionBadge(): string
{
    return UlidPolisher::adminFormat($this->version_id);
}

// ✅ In Blade templates
<span class="font-mono">{{ UlidPolisher::short($model->version_id) }}</span>
<div class="badge">{{ UlidPolisher::display($model->version_id) }}</div>

// ✅ In API resources
'version' => UlidPolisher::short($this->version_id),
'display_version' => UlidPolisher::display($this->version_id),

// ✅ In DTOs, transformers, anywhere you need formatted data
$dto->version = UlidPolisher::short($rawData['version_id']);
$processedData = array_map(
    fn($item) => UlidPolisher::display($item['id']), 
    $apiResponse
);
```

**The incredible result**: One beautifully simple polisher class powering rock-solid consistent formatting across Filament tables, Livewire components, Blade views, API responses, background jobs, notifications, DTOs, and literally anywhere else you need data presentation in your Laravel application.

This is what "write once, use everywhere" actually feels like.

## Built-in Polishers: 11 Production-Ready Methods

Polish ships with two battle-tested polishers featuring 11 carefully crafted methods that complement (never duplicate) Laravel's existing `Number` and `Str` classes. Start using them today, or study their implementation to master your own polisher creation:

### NumberPolisher: 5 Methods for Advanced Number Display

Perfect for rankings, progress indicators, and user-facing number displays:

```php
use Hdaklue\Polish\NumberPolisher;

// Ordinal numbers - perfect for rankings, dates, positions
NumberPolisher::ordinal(1);    // "1st" 
NumberPolisher::ordinal(22);   // "22nd"  
NumberPolisher::ordinal(103);  // "103rd"

// Roman numerals (1-399) - great for versions, chapters, generations
NumberPolisher::roman(42);     // "XLII"
NumberPolisher::roman(399);    // "CCCXCIX"
NumberPolisher::roman(400);    // "400" (smart fallback for out-of-range)

// Number ranges - clean display for filters, reports, spans
NumberPolisher::range(10, 20);           // "10–20" (em dash, not hyphen)
NumberPolisher::range(1, 5, ' to ');     // "1 to 5" (custom separator)

// Score formatting - test scores, ratings, performance metrics  
NumberPolisher::score(85, 100);          // "85/100"
NumberPolisher::score(7, 10);            // "7/10"

// Visual star ratings - reviews, quality indicators, user ratings
NumberPolisher::rating(4.5);             // "★★★★☆" (out of 5 stars)
NumberPolisher::rating(7.5, 10);         // "★★★★★★★☆☆☆" (custom max)
```

**Real-world impact**: Transform boring numbers into engaging user experiences in leaderboards, product ratings, test scores, version displays, progress indicators, and anywhere you need numbers to feel more human and less mechanical.

### StringPolisher: 6 Methods for String Excellence

Professional string operations that go beyond Laravel's `Str` class, handling the complex formatting scenarios you face daily:

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
StringPolisher::mention('John Doe');                     // "@john_doe"
StringPolisher::hashtag('React Native');                 // "#ReactNative"
```

## Real-World Use Cases: From Concept to Code

See Polish in action solving actual formatting challenges developers face every day:

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

## Framework Integration: Works Everywhere You Do

Polish integrates seamlessly with every part of the Laravel ecosystem you're already using:

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

## Creating Custom Polishers: Unleash Your Creativity

Polish truly shines when you create domain-specific polishers tailored to your application's unique needs. Here's how to build powerful, reusable formatting logic that will make your future self grateful:

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

### Domain-Specific Logic: The Sky's the Limit

Polish adapts to any industry or domain. Here are just a few examples of what developers are building:

- **Legal**: Case numbers, statute formatting, citation styles, court document references
- **Medical**: Dosage formatting, medical record numbers, patient ID masking, prescription displays
- **Financial**: Transaction IDs, account masking, audit trails, routing number formatting
- **Gaming**: Player stats, achievement badges, leaderboards, XP displays, guild rankings
- **Education**: Grade formatting, student IDs, course codes, transcript displays
- **Real Estate**: Property codes, listing formats, MLS numbers, address standardization
- **E-commerce**: SKU formatting, inventory displays, shipping tracking, discount codes
- **SaaS**: Subscription tiers, usage metrics, billing displays, feature flags

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

The ultimate goal is to eliminate scattered formatting logic and create a clean, testable, and maintainable system for your presentation layer that actually brings joy to your development process.

## Advanced Features: The Details That Matter

### Automatic Method Validation
Polish includes intelligent reflection-based validation that catches typos before they become bugs:

```php
UlidPolisher::invalidMethod($ulid); 
// Throws: UnsupportedPolisherMethodException: 
// Polisher [App\Polishers\UlidPolisher] does not support static polish method [invalidMethod].
```

No more silent failures or mysterious undefined method errors — Polish tells you exactly what went wrong and how to fix it.

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

## Why Polish? The Compelling Case

Polish isn't just another formatting library — it's a paradigm shift that solves real developer pain points:

### The Technical Benefits
- **Single Source of Truth** — Change formatting logic once, see it instantly everywhere across your application
- **Framework Agnostic** — Works flawlessly in Filament, Livewire, Blade, APIs, DTOs, jobs, notifications, anywhere
- **Static-First Architecture** — No dependency injection complexity, no service location overhead, just clean static calls
- **Zero Configuration** — Extend `BasePolisher`, add methods, start using immediately with no setup friction
- **Laravel Native Integration** — Auto-discovery, follows Laravel conventions, feels like part of the framework
- **Superior Developer Experience** — Clear exceptions, predictable API, familiar patterns you already know

### The Business Impact
- **Reduced Maintenance Overhead** — Stop hunting down scattered formatting code across your application
- **Faster Feature Development** — New features inherit consistent formatting automatically  
- **Improved Code Quality** — Centralized logic means better testing, easier debugging, cleaner code reviews
- **Team Efficiency** — Onboarding developers understand formatting patterns instantly
- **Future-Proof Architecture** — Changes scale effortlessly without breaking existing implementations

### The Developer Experience
Polish feels like what Laravel formatting should have been from the beginning — intuitive, powerful, and delightfully simple to use.

---

## Start Polishing in the Next 5 Minutes

Ready to transform your formatting chaos into elegant, maintainable code? Here's your quickstart path:

1. **Install Polish** (30 seconds)
   ```bash
   composer require hdaklue/polish
   ```

2. **Create your first polisher** (60 seconds)
   ```bash
   php artisan polisher:make User
   ```

3. **Add a method** (90 seconds)
   ```php
   public static function displayName(string $firstName, string $lastName): string
   {
       return "{$firstName} {$lastName}";
   }
   ```

4. **Use it everywhere** (The rest is history)
   ```php
   {{ UserPolisher::displayName($user->first_name, $user->last_name) }}
   ```

**In under 5 minutes, you'll have eliminated scattered formatting logic and created your first centralized polisher.** Your future self will thank you every time you need to update that formatting logic and it happens instantly across your entire application.

## Requirements

- PHP 8.1+
- Laravel 9.0+

## License

MIT