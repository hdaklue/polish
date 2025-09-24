<?php

namespace Hdaklue\Polish\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputArgument;

class MakePolisherCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     */
    protected $name = 'polisher:make';

    /**
     * The console command description.
     */
    protected $description = 'Create a new polisher class';

    /**
     * The type of class being generated.
     */
    protected $type = 'Polisher';

    /**
     * Get the stub file for the generator.
     */
    protected function getStub(): string
    {
        return __DIR__.'/stubs/polisher.stub';
    }

    /**
     * Get the default namespace for the class.
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\Polishers';
    }

    /**
     * Get the console command arguments.
     */
    protected function getArguments(): array
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the polisher'],
        ];
    }

    /**
     * Build the class with the given name.
     */
    protected function buildClass($name): string
    {
        $stub = $this->files->get($this->getStub());

        return $this->replaceNamespace($stub, $name)->replaceClass($stub, $name);
    }
}