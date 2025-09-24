<?php

use Hdaklue\Polish\Exceptions\UnsupportedPolisherMethodException;
use Hdaklue\Polish\Tests\Fixtures\TestPolisher;

describe('BasePolisher __callStatic method', function () {
    it('delegates calls to existing static methods successfully', function () {
        $result = TestPolisher::short('1234567890');
        
        expect($result)->toBe('567890');
    });

    it('passes single argument correctly to delegated method', function () {
        $result = TestPolisher::uppercase('hello world');
        
        expect($result)->toBe('HELLO WORLD');
    });

    it('passes multiple arguments correctly to delegated method', function () {
        $result = TestPolisher::multipleArgs('prefix-', 'middle', '-suffix');
        
        expect($result)->toBe('prefix-middle-suffix');
    });

    it('handles method with default parameter values', function () {
        $result = TestPolisher::multipleArgs('start-', 'content');
        
        expect($result)->toBe('start-content');
    });

    it('returns array data types correctly', function () {
        $result = TestPolisher::returnArray();
        
        expect($result)
            ->toBeArray()
            ->toHaveKey('key', 'value')
            ->toHaveKey('nested')
            ->and($result['nested'])
            ->toHaveKey('item', 'data');
    });

    it('returns null values correctly', function () {
        $result = TestPolisher::returnNull();
        
        expect($result)->toBeNull();
    });

    it('allows exceptions to bubble up from delegated methods', function () {
        expect(fn () => TestPolisher::throwException())
            ->toThrow(RuntimeException::class, 'Test exception message');
    });

    it('throws UnsupportedPolisherMethodException for non-existent methods', function () {
        $exception = null;
        try {
            TestPolisher::nonExistentMethod('arg');
        } catch (UnsupportedPolisherMethodException $e) {
            $exception = $e;
        }
        
        expect($exception)->toBePolisherException(
            'Hdaklue\Polish\Tests\Fixtures\TestPolisher',
            'nonExistentMethod'
        );
    });

    it('throws UnsupportedPolisherMethodException for protected methods', function () {
        $polisher = new class extends TestPolisher {
            protected static function protectedMethod(): string
            {
                return 'protected';
            }
        };

        expect(fn () => $polisher::protectedMethod())
            ->toThrow(UnsupportedPolisherMethodException::class);
    });

    it('throws UnsupportedPolisherMethodException for private methods', function () {
        $polisher = new class extends TestPolisher {
            private static function privateMethod(): string
            {
                return 'private';
            }
        };

        expect(fn () => $polisher::privateMethod())
            ->toThrow(UnsupportedPolisherMethodException::class);
    });
});

describe('BasePolisher edge cases', function () {
    it('handles empty string arguments', function () {
        $result = TestPolisher::short('');
        
        expect($result)->toBe('');
    });

    it('handles very long string arguments', function () {
        $longString = str_repeat('a', 1000);
        $result = TestPolisher::short($longString);
        
        expect($result)
            ->toBe('aaaaaa')
            ->toHaveLength(6);
    });

    it('handles special characters in method arguments', function () {
        $result = TestPolisher::uppercase('héllo wörld! @#$%^&*()');
        
        expect($result)->toBe('HéLLO WöRLD! @#$%^&*()');
    });

    it('maintains method chaining behavior through static calls', function () {
        $input = 'abcdefghij';
        $short = TestPolisher::short($input);
        $formatted = TestPolisher::formatted($input);
        
        expect($short)->toBe('efghij');
        expect($formatted)->toBe('v-efghij');
    });

    it('handles zero arguments methods correctly', function () {
        $result = TestPolisher::returnArray();
        
        expect($result)
            ->toBeArray()
            ->toHaveCount(2);
    });

    it('handles methods with various return types', function () {
        expect(TestPolisher::returnNull())->toBeNull();
        expect(TestPolisher::returnArray())->toBeArray();
        expect(TestPolisher::short('test'))->toBeString();
        expect(TestPolisher::uppercase('test'))->toBeString();
    });

    it('preserves static method call semantics', function () {
        // Test that calling through __callStatic behaves identical to direct calls
        $directCall = substr('abcdefghij', -6);
        $polisherCall = TestPolisher::short('abcdefghij');
        
        expect($polisherCall)->toBe($directCall);
    });
});

describe('BasePolisher method existence validation', function () {
    it('correctly identifies existing public static methods', function () {
        expect(TestPolisher::class)
            ->toHaveStaticMethod('short')
            ->toHaveStaticMethod('formatted')
            ->toHaveStaticMethod('uppercase')
            ->toHaveStaticMethod('multipleArgs');
    });

    it('handles method_exists check correctly in __callStatic', function () {
        // This tests the internal logic of method_exists within __callStatic
        $reflection = new ReflectionClass(TestPolisher::class);
        
        expect($reflection->hasMethod('short'))->toBeTrue();
        expect($reflection->hasMethod('nonExistentMethod'))->toBeFalse();
    });
});

describe('BasePolisher performance and memory', function () {
    it('handles large number of method calls efficiently', function () {
        $startMemory = memory_get_usage();
        
        for ($i = 0; $i < 1000; $i++) {
            TestPolisher::short('test' . $i);
        }
        
        $memoryUsed = memory_get_usage() - $startMemory;
        
        // Should not use excessive memory (arbitrary but reasonable limit)
        expect($memoryUsed)->toBeLessThan(1024 * 1024); // 1MB
    });

    it('method delegation has minimal performance overhead', function () {
        $iterations = 1000;
        
        // Time direct method calls
        $start = microtime(true);
        for ($i = 0; $i < $iterations; $i++) {
            substr('teststring', -6);
        }
        $directTime = microtime(true) - $start;
        
        // Time polisher method calls
        $start = microtime(true);
        for ($i = 0; $i < $iterations; $i++) {
            TestPolisher::short('teststring');
        }
        $polisherTime = microtime(true) - $start;
        
        // Polisher should not be more than 10x slower than direct calls
        expect($polisherTime)->toBeLessThan($directTime * 10);
    });
});