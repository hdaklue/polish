<?php

use Hdaklue\Polish\StringPolisher;

test('smartMask method masks email addresses correctly', function () {
    expect(StringPolisher::smartMask('john.doe@email.com', 'email'))
        ->toBe('jo******@email.com')
        ->and(StringPolisher::smartMask('a@b.com', 'email'))
        ->toBe('a@b.com'); // Too short to mask
});

test('smartMask method masks phone numbers correctly', function () {
    expect(StringPolisher::smartMask('555-123-4567', 'phone'))
        ->toBe('555-***-4567')
        ->and(StringPolisher::smartMask('1234567890', 'phone'))
        ->toBe('123***7890');
});

test('smartMask method masks card numbers correctly', function () {
    expect(StringPolisher::smartMask('4111-1111-1111-1111', 'card'))
        ->toBe('4111-****-****-1111')
        ->and(StringPolisher::smartMask('4111111111111111', 'card'))
        ->toBe('4111********1111');
});

test('smartMask method uses default masking for unknown types', function () {
    $result = StringPolisher::smartMask('sensitive data', 'unknown');
    expect($result)->toContain('***');
});

test('excerpt method creates basic excerpts', function () {
    $text = 'This is a long text that should be truncated to a specific length for display purposes.';
    $result = StringPolisher::excerpt($text, 50);
    
    expect(strlen($result))->toBeLessThan(54) // 50 + '...'
        ->and($result)->toContain('...');
});

test('excerpt method centers excerpt around keyword', function () {
    $text = 'This is a very long text with an important keyword that we want to highlight in the excerpt.';
    $result = StringPolisher::excerpt($text, 50, 'keyword');
    
    expect($result)->toContain('keyword')
        ->and($result)->toContain('...');
});

test('excerpt method handles keyword at beginning', function () {
    $text = 'keyword at the beginning of a very long text that continues for a while.';
    $result = StringPolisher::excerpt($text, 30, 'keyword');
    
    expect($result)->toContain('keyword')
        ->and($result)->not->toStartWith('...');
});

test('excerpt method handles keyword at end', function () {
    $text = 'This is a very long text that ends with the important keyword';
    $result = StringPolisher::excerpt($text, 30, 'keyword');
    
    expect($result)->toContain('keyword')
        ->and($result)->not->toEndWith('...');
});

test('excerpt method strips HTML tags', function () {
    $html = '<p>This is <strong>HTML</strong> content with <a href="#">links</a></p>';
    $result = StringPolisher::excerpt($html, 50);
    
    expect($result)->not->toContain('<')
        ->and($result)->not->toContain('>');
});

test('humanize method converts technical strings to human readable', function () {
    expect(StringPolisher::humanize('user_profile_settings'))
        ->toBe('User Profile Settings')
        ->and(StringPolisher::humanize('api_v2_endpoint'))
        ->toBe('API V2 Endpoint')
        ->and(StringPolisher::humanize('payment-gateway-config'))
        ->toBe('Payment Gateway Config')
        ->and(StringPolisher::humanize('camelCaseVariable'))
        ->toBe('Camel Case Variable');
});

test('humanize method handles acronyms correctly', function () {
    expect(StringPolisher::humanize('rest_api_url'))
        ->toBe('Rest API URL')
        ->and(StringPolisher::humanize('json_response_handler'))
        ->toBe('JSON Response Handler')
        ->and(StringPolisher::humanize('html_parser'))
        ->toBe('HTML Parser');
});

test('humanize method handles version numbers', function () {
    expect(StringPolisher::humanize('api_v1_controller'))
        ->toBe('API V1 Controller')
        ->and(StringPolisher::humanize('database_v3_migration'))
        ->toBe('Database V3 Migration');
});

test('initials method extracts initials correctly', function () {
    expect(StringPolisher::initials('John Doe'))
        ->toBe('JD')
        ->and(StringPolisher::initials('John Michael Doe'))
        ->toBe('JM')
        ->and(StringPolisher::initials('John Michael Doe', 3))
        ->toBe('JMD')
        ->and(StringPolisher::initials('Single'))
        ->toBe('S');
});

test('initials method handles empty and extra spaces', function () {
    expect(StringPolisher::initials('  John   Doe  '))
        ->toBe('JD')
        ->and(StringPolisher::initials(''))
        ->toBe('');
});

test('mention method creates mention format', function () {
    expect(StringPolisher::mention('John Doe'))
        ->toBe('@john-doe')
        ->and(StringPolisher::mention('User Name 123'))
        ->toBe('@user-name-123');
});

test('hashtag method creates hashtag format', function () {
    expect(StringPolisher::hashtag('React Native'))
        ->toBe('#ReactNative')
        ->and(StringPolisher::hashtag('JavaScript ES6'))
        ->toBe('#JavaScriptES6')
        ->and(StringPolisher::hashtag('Node.js API'))
        ->toBe('#NodejsAPI');
});

test('hashtag method removes special characters', function () {
    expect(StringPolisher::hashtag('Test@#$%^&*()'))
        ->toBe('#Test');
});

test('StringPolisher extends BasePolisher', function () {
    expect(StringPolisher::class)
        ->toHaveStaticMethod('smartMask')
        ->toHaveStaticMethod('excerpt')
        ->toHaveStaticMethod('humanize')
        ->toHaveStaticMethod('initials')
        ->toHaveStaticMethod('mention')
        ->toHaveStaticMethod('hashtag');
});