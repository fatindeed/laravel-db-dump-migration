<?php

namespace Fatindeed\LaravelDbDumpMigration\Generator;

use InvalidArgumentException;
use Illuminate\Support\Facades\DB;

class DataProvider
{
    /**
     * Table schema.
     *
     * @var array
     */
    protected $schema;

    /**
     * Create a new data provider instance.
     *
     * @param  string  $connection
     * @param  string  $table
     * @return void
     * 
     * @throws \InvalidArgumentException
     */
    public function __construct(string $connection, string $table)
    {
        DB::setDefaultConnection($connection);
        if (DB::connection()->getDriverName() != 'mysql') {
            throw new InvalidArgumentException('Only support MySQL driver.');
        }
        $schema = DB::selectOne('select * from information_schema.TABLES where TABLE_SCHEMA = ? AND TABLE_NAME = ?', [DB::connection()->getDatabaseName(), $table]);
        if (is_null($schema)) {
            throw new InvalidArgumentException('Table "'.$table.'" does not exist.');
        }
        $this->schema = (array) $schema;
    }

    /**
     * Get schema.
     *
     * @return array
     */
    public function getSchema(): array
    {
        return $this->schema;
    }

    /**
     * Get columns.
     *
     * @return array
     */
    public function getColumns(): array
    {
        return DB::select('select * from information_schema.COLUMNS where TABLE_SCHEMA = ? AND TABLE_NAME = ?', [$this->schema['TABLE_SCHEMA'], $this->schema['TABLE_NAME']]);
    }

    /**
     * Get indexes.
     *
     * @return array
     */
    public function getIndexes(): array
    {
        $keys = DB::select('select * from information_schema.KEY_COLUMN_USAGE where TABLE_SCHEMA = ? AND TABLE_NAME = ?', [$this->schema['TABLE_SCHEMA'], $this->schema['TABLE_NAME']]);
        return $keys;
    }
}
