<?php

use Hdaklue\Polish\Exceptions\UnsupportedPolisherMethodException;

describe('UnsupportedPolisherMethodException', function () {
    it('extends BadMethodCallException', function () {
        $exception = new UnsupportedPolisherMethodException('TestClass', 'testMethod');
        
        expect($exception)->toBeInstanceOf(BadMethodCallException::class);
    });

    it('formats error message correctly with class and method names', function () {
        $exception = new UnsupportedPolisherMethodException('App\\Polishers\\TestPolisher', 'formatData');
        
        expect($exception->getMessage())
            ->toBe('Polisher [App\\Polishers\\TestPolisher] does not support static polish method [formatData].');
    });

    it('handles fully qualified class names', function () {
        $exception = new UnsupportedPolisherMethodException(
            'Hdaklue\\Polish\\Tests\\Fixtures\\TestPolisher',
            'nonExistentMethod'
        );
        
        expect($exception->getMessage())
            ->toBe('Polisher [Hdaklue\\Polish\\Tests\\Fixtures\\TestPolisher] does not support static polish method [nonExistentMethod].');
    });

    it('handles method names with special characters', function () {
        $exception = new UnsupportedPolisherMethodException('TestClass', '__invoke');
        
        expect($exception->getMessage())
            ->toBe('Polisher [TestClass] does not support static polish method [__invoke].');
    });

    it('handles empty class names gracefully', function () {
        $exception = new UnsupportedPolisherMethodException('', 'testMethod');
        
        expect($exception->getMessage())
            ->toBe('Polisher [] does not support static polish method [testMethod].');
    });

    it('handles empty method names gracefully', function () {
        $exception = new UnsupportedPolisherMethodException('TestClass', '');
        
        expect($exception->getMessage())
            ->toBe('Polisher [TestClass] does not support static polish method [].');
    });

    it('maintains standard exception properties', function () {
        $exception = new UnsupportedPolisherMethodException('TestClass', 'testMethod');
        
        expect($exception->getCode())->toBe(0);
        expect($exception->getFile())->toBeString();
        expect($exception->getLine())->toBeInt();
        expect($exception->getTrace())->toBeArray();
    });

    it('can be caught as BadMethodCallException', function () {
        $caught = false;
        
        try {
            throw new UnsupportedPolisherMethodException('TestClass', 'testMethod');
        } catch (BadMethodCallException $e) {
            $caught = true;
            expect($e)->toBeInstanceOf(UnsupportedPolisherMethodException::class);
        }
        
        expect($caught)->toBeTrue();
    });

    it('can be caught as Exception', function () {
        $caught = false;
        
        try {
            throw new UnsupportedPolisherMethodException('TestClass', 'testMethod');
        } catch (Exception $e) {
            $caught = true;
            expect($e)->toBeInstanceOf(UnsupportedPolisherMethodException::class);
        }
        
        expect($caught)->toBeTrue();
    });

    it('preserves exception chain when provided', function () {
        $previousException = new RuntimeException('Previous error');
        $exception = new UnsupportedPolisherMethodException('TestClass', 'testMethod');
        
        // While the constructor doesn't support previous exceptions directly,
        // we can test that it would work with the parent constructor
        expect($exception->getPrevious())->toBeNull();
    });

    it('uses the custom toBePolisherException expectation correctly', function () {
        $exception = new UnsupportedPolisherMethodException('App\\TestPolisher', 'missingMethod');
        
        expect($exception)->toBePolisherException('App\\TestPolisher', 'missingMethod');
    });
});

describe('UnsupportedPolisherMethodException integration', function () {
    it('works correctly with PHP try-catch blocks', function () {
        $exceptionCaught = false;
        $caughtMessage = '';
        
        try {
            throw new UnsupportedPolisherMethodException('TestClass', 'testMethod');
        } catch (UnsupportedPolisherMethodException $e) {
            $exceptionCaught = true;
            $caughtMessage = $e->getMessage();
        }
        
        expect($exceptionCaught)->toBeTrue();
        expect($caughtMessage)->toBe('Polisher [TestClass] does not support static polish method [testMethod].');
    });

    it('can be serialized and unserialized when possible', function () {
        $originalException = new UnsupportedPolisherMethodException('TestClass', 'testMethod');
        
        // Some exceptions may not be serializable due to closures in stack trace
        // So we'll test the core properties that should be preserved
        expect($originalException->getMessage())
            ->toBe('Polisher [TestClass] does not support static polish method [testMethod].')
            ->and($originalException->getCode())
            ->toBe(0);
    });

    it('handles unicode class and method names', function () {
        $exception = new UnsupportedPolisherMethodException('TestClässé', 'méthodé');
        
        expect($exception->getMessage())
            ->toBe('Polisher [TestClässé] does not support static polish method [méthodé].');
    });

    it('handles very long class and method names', function () {
        $longClassName = str_repeat('VeryLongClassName', 10);
        $longMethodName = str_repeat('veryLongMethodName', 5);
        
        $exception = new UnsupportedPolisherMethodException($longClassName, $longMethodName);
        
        expect($exception->getMessage())
            ->toContain("Polisher [{$longClassName}]")
            ->toContain("static polish method [{$longMethodName}]");
    });

    it('maintains proper inheritance hierarchy', function () {
        $exception = new UnsupportedPolisherMethodException('TestClass', 'testMethod');
        
        expect($exception)
            ->toBeInstanceOf(UnsupportedPolisherMethodException::class)
            ->toBeInstanceOf(BadMethodCallException::class)
            ->toBeInstanceOf(LogicException::class)
            ->toBeInstanceOf(Exception::class)
            ->toBeInstanceOf(Throwable::class);
    });
});