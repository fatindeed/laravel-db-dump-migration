<?php

namespace Fatindeed\LaravelDbDumpMigration\Schema;

use InvalidArgumentException;

class Column
{
    protected static $casts = [
        'bit' => 'boolean',
        'year' => 'year',
        'date' => 'date',
        'time' => 'time',
        'datetime' => 'dateTime',
        'timestamp' => 'timestamp',
        'text' => 'text',
        'mediumtext' => 'mediumText',
        'longtext' => 'longText',
        // 'json' => 'json',
        // 'jsonb' => 'jsonb',
        'geometry' => 'geometry',
        'geometrycollection' => 'geometryCollection',
        'linestring' => 'lineString',
        'multilinestring' => 'multiLineString',
        'point' => 'point',
        'multipoint' => 'multiPoint',
        'polygon' => 'polygon',
        'multipolygon' => 'multiPolygon',
    ];

    protected static $integer_types = ['bigint', 'int', 'mediumint', 'smallint', 'tinyint'];

    /**
     * The column data.
     *
     * @var stdClass
     */
    protected $data;

    /**
     * Create a new migration creator instance.
     *
     * @param  stdClass  $data
     * @return void
     */
    public function __construct(stdClass $data)
    {
        $this->data = $data;
    }

    /**
     * Get migration command.
     *
     * @return string
     * 
     * @throws \InvalidArgumentException
     */
    public function getMigrationCommand(): string
    {
        $content = '$table';
        if ($this->data->EXTRA == 'auto_increment' && in_array($this->data->DATA_TYPE, self::$integer_types)) {
            $columnType = substr($this->data->DATA_TYPE, 0, -4).'Increments';
            $content .= '->'.lcfirst($columnType).'('.self::quote($this->data->COLUMN_NAME).');';
            return $content;
        }
        $quoteValue = true;
        if (isset(self::$casts[$this->data->DATA_TYPE])) {
            if ($this->data->DATA_TYPE == 'bit') {
                $quoteValue = false;
            }
            $content .= '->'.self::$casts[$this->data->DATA_TYPE].'('.self::quote($this->data->COLUMN_NAME).')';
        } else if ($this->data->DATA_TYPE == 'char' || $this->data->DATA_TYPE == 'varchar') {
            $content .= '->'.($this->data->DATA_TYPE == 'char' ? 'char' : 'string').'('.self::quote($this->data->COLUMN_NAME).', '.$this->data->CHARACTER_MAXIMUM_LENGTH.')';
        } else if (in_array($this->data->DATA_TYPE, self::$integer_types)) {
            $quoteValue = false;
            $columnType = substr($this->data->DATA_TYPE, 0, -4).'Integer';
            if (strpos($this->data->COLUMN_TYPE, 'unsigned') !== false) {
                $content .= '->unsigned'.ucfirst($columnType).'('.self::quote($this->data->COLUMN_NAME).')';
            } else {
                $content .= '->'.lcfirst($columnType).'('.self::quote($this->data->COLUMN_NAME).')';
            }
        } else if ($this->data->DATA_TYPE == 'decimal') {
            $quoteValue = false;
            if (strpos($this->data->COLUMN_TYPE, 'unsigned') !== false) {
                $content .= '->unsignedDecimal('.self::quote($this->data->COLUMN_NAME).', '.$this->data->NUMERIC_PRECISION.', '.$this->data->NUMERIC_SCALE.')';
            } else {
                $content .= '->decimal('.self::quote($this->data->COLUMN_NAME).', '.$this->data->NUMERIC_PRECISION.', '.$this->data->NUMERIC_SCALE.')';
            }
        } else if ($this->data->DATA_TYPE == 'double' || $this->data->DATA_TYPE == 'float') {
            $quoteValue = false;
            $content .= '->'.$this->data->DATA_TYPE.'('.self::quote($this->data->COLUMN_NAME).', '.$this->data->NUMERIC_PRECISION.', '.$this->data->NUMERIC_SCALE.')';
        } else if ($this->data->DATA_TYPE == 'enum' || $this->data->DATA_TYPE == 'set') {
            $start = strlen($this->data->DATA_TYPE) + 1;
            $content .= '->'.$this->data->DATA_TYPE.'('.self::quote($this->data->COLUMN_NAME).', ['.substr($this->data->COLUMN_TYPE, $start, -1).'])';
        } else if ($this->data->DATA_TYPE == 'blob' || $this->data->DATA_TYPE == 'longblob') {
            $content .= '->binary('.self::quote($this->data->COLUMN_NAME).')';
        } else {
            throw new InvalidArgumentException('Unsupported column type: '.$this->data->DATA_TYPE.'.');
        }
        if ($this->data->IS_NULLABLE == 'YES') {
            $content .= '->nullable()';
        }
        if ($this->data->COLUMN_DEFAULT != null) {
            $content .= '->default('.($quoteValue ? self::quote($this->data->COLUMN_DEFAULT) : $this->data->COLUMN_DEFAULT).')';
        }
        // ->useCurrent()
        if ($this->data->COLUMN_KEY == 'PRI') {
            $content .= '->primary()';
        }
        if ($this->data->COLUMN_COMMENT) {
            $content .= '->comment('.self::quote($this->data->COLUMN_COMMENT).')';
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
