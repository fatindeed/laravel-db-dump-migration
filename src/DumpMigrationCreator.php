<?php

namespace Fatindeed\LaravelDbDumpMigration;

use InvalidArgumentException;
use Illuminate\Support\Str;

class DumpMigrationCreator
{
    /**
     * Create a new migration at the given path.
     *
     * @param  string  $table
     * @param  string  $database
     * @param  string  $path
     * @return string
     *
     * @throws \Exception
     */
    public function create($table, $database, $path)
    {
        $name = Str::snake('create_'.$table.'_table');
        $this->ensureMigrationDoesntAlreadyExist($name);

        $generator = new Generator\TableGenerator(new Generator\DataProvider($database, $table));

        $stub = file_get_contents($this->stubPath().'/create.stub');
        $stub = str_replace('DummyClass', $this->getClassName($name), $stub);
        $stub = str_replace('DummyTable', $table, $stub);
        $stub = str_replace('// blueprint placeholder'.PHP_EOL, $generator->getBlueprint(str_pad('', 12)), $stub);
        file_put_contents($path = $this->getPath($name, $path), $stub);

        return $path;
    }

    /**
     * Ensure that a migration with the given name doesn't already exist.
     *
     * @param  string  $name
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    protected function ensureMigrationDoesntAlreadyExist($name)
    {
        if (class_exists($className = $this->getClassName($name))) {
            throw new InvalidArgumentException("A {$className} class already exists.");
        }
    }

    /**
     * Populate the place-holders in the migration stub.
     *
     * @param  string  $name
     * @param  string  $stub
     * @param  string|null  $table
     * @return string
     */
    protected function populateStub($name, $stub, $table)
    {
        return $stub;
    }

    /**
     * Get the class name of a migration name.
     *
     * @param  string  $name
     * @return string
     */
    protected function getClassName($name)
    {
        return Str::studly($name);
    }

    /**
     * Get the full path to the migration.
     *
     * @param  string  $name
     * @param  string  $path
     * @return string
     */
    protected function getPath($name, $path)
    {
        return $path.'/'.$this->getDatePrefix().'_'.$name.'.php';
    }

    /**
     * Get the date prefix for the migration.
     *
     * @return string
     */
    protected function getDatePrefix()
    {
        return date('Y_m_d_His');
    }

    /**
     * Get the path to the stubs.
     *
     * @return string
     */
    public function stubPath()
    {
        return __DIR__.'/stubs';
    }
}
