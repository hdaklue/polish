<?php

use Hdaklue\Polish\PolishServiceProvider;

describe('PolishServiceProvider', function () {
    it('is automatically registered via Laravel package discovery', function () {
        $providers = $this->app->getLoadedProviders();
        
        expect($providers)->toHaveKey(PolishServiceProvider::class);
    });

    it('boots successfully without errors', function () {
        $provider = new PolishServiceProvider($this->app);
        
        expect(fn () => $provider->boot())->not->toThrow(Exception::class);
    });

    it('register method exists and runs without errors', function () {
        $provider = new PolishServiceProvider($this->app);
        
        expect(fn () => $provider->register())->not->toThrow(Exception::class);
    });

    it('extends Laravel ServiceProvider correctly', function () {
        $provider = new PolishServiceProvider($this->app);
        
        expect($provider)->toBeInstanceOf(\Illuminate\Support\ServiceProvider::class);
    });

    it('has required service provider methods', function () {
        expect(PolishServiceProvider::class)
            ->toHaveMethod('register')
            ->toHaveMethod('boot');
    });
});