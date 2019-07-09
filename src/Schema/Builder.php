<?php

namespace Fatindeed\LaravelDbDumpMigration\Schema;

use InvalidArgumentException;
use Illuminate\Support\Facades\DB;

/**
 * @see https://laravel.com/docs/5.8/migrations
 */
class Builder
{
    /**
     * The name of the connected database.
     *
     * @var string
     */
    protected $database;

    /**
     * The table to dump.
     *
     * @var string
     */
    protected $table;

    /**
     * Create a new migration creator instance.
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
        $this->database = DB::connection()->getDatabaseName();
        // $data = DB::selectOne('select * from information_schema.TABLES where TABLE_SCHEMA = ? AND TABLE_NAME = ?', [$this->database, $table]);
        // if (is_null($data)) {
        //     throw new InvalidArgumentException('Table "'.$table.'" does not exist.');
        // }
        $this->table = $table;
    }

    /**
     * Get Blueprint.
     *
     * @param  string  $prefix
     * @return string
     */
    public function getBlueprint(string $prefix = ''): string
    {
        return $this->getColumnMigrations($prefix).$this->getIndexMigrations($prefix);
    }

    /**
     * Get column migrations.
     *
     * @param  string  $prefix
     * @return string
     */
    public function getColumnMigrations(string $prefix = ''): string
    {
        $content = '';
        $rows = DB::select('select * from information_schema.COLUMNS where TABLE_SCHEMA = ? AND TABLE_NAME = ?', [$this->database, $this->table]);
        foreach ($rows as $data) {
            $column = new Column($data);
            $content .= $prefix.$column->getMigrationCommand();
        }
        return $content;
    }

    /**
     * Get index migrations.
     *
     * @param  string  $prefix
     * @return string
     */
    public function getIndexMigrations(string $prefix = ''): string
    {
        $content = '';
        $indexes = DB::select('select * from information_schema.KEY_COLUMN_USAGE where TABLE_SCHEMA = ? AND TABLE_NAME = ?', [$database, $table]);
        foreach ($indexes as $index) {
            if ($index->CONSTRAINT_NAME == 'PRIMARY') {
                continue;
            }
            // $table->unique('email');
            $content .= $prefix.'$table->index(\''.$index->COLUMN_NAME.'\');';
        }
        return $content;
    }

    /**
     * Quote text.
     *
     * @param  string  $text
     * @return string
     */
    protected static function quote(string $text): string
    {
        return '\''.$text.'\'';
    }
}
