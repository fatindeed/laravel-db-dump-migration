<?php

namespace Fatindeed\LaravelDbDumpMigration\Generator;

use ArrayObject;
use InvalidArgumentException;

/**
 * Column generator class
 */
class ColumnGenerator extends ArrayObject
{
    const COLUMN_TYPE_NORMAL = 0;
    const COLUMN_TYPE_STRING = 1;
    const COLUMN_TYPE_INTEGER = 2;
    const COLUMN_TYPE_FLOAT = 3;
    const COLUMN_TYPE_ENUM = 4;
    const COLUMN_TYPE_INCREMENT = 5;

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
        'blob' => 'binary',
        'longblob' => 'binary',
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

    /**
     * Create a new migration creator instance.
     *
     * @param \stdClass $data Column filed meta data
     */
    public function __construct(\stdClass $data)
    {
        parent::__construct((array) $data, ArrayObject::ARRAY_AS_PROPS);
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
        $category = self::COLUMN_TYPE_NORMAL;
        $columnArgs = [self::quote($this->COLUMN_NAME)];
        if (isset(self::$casts[$this->DATA_TYPE])) {
            if ($this->DATA_TYPE == 'bit') {
                $category = self::COLUMN_TYPE_INTEGER;
            }
            $columnFunc = self::$casts[$this->DATA_TYPE];
        } else if ($this->DATA_TYPE == 'char' || $this->DATA_TYPE == 'varchar') {
            $columnFunc = ($this->DATA_TYPE == 'char' ? 'char' : 'string');
            $columnArgs[] = $this->CHARACTER_MAXIMUM_LENGTH;
            $category = self::COLUMN_TYPE_STRING;
        } else if (in_array($this->DATA_TYPE, ['bigint', 'int', 'mediumint', 'smallint', 'tinyint'])) {
            $columnFunc = lcfirst(substr($this->DATA_TYPE, 0, -4) . 'Integer');
            $category = self::COLUMN_TYPE_INTEGER;
        } else if ($this->DATA_TYPE == 'decimal' || $this->DATA_TYPE == 'double' || $this->DATA_TYPE == 'float') {
            $columnFunc = $this->DATA_TYPE;
            $columnArgs[] = $this->NUMERIC_PRECISION;
            $columnArgs[] = $this->NUMERIC_SCALE;
            $category = self::COLUMN_TYPE_FLOAT;
        } else if ($this->DATA_TYPE == 'enum' || $this->DATA_TYPE == 'set') {
            $columnFunc = $this->DATA_TYPE;
            $columnArgs[] = '[' . substr($this->COLUMN_TYPE, (strlen($this->DATA_TYPE) + 1), -1) . ']';
            $category = self::COLUMN_TYPE_ENUM;
        } else {
            throw new InvalidArgumentException('Unsupported column type: ' . $this->DATA_TYPE . '.');
        }
        if ($this->COLUMN_KEY == 'PRI' && $this->EXTRA == 'auto_increment' && $category == self::COLUMN_TYPE_INTEGER) {
            $columnFunc = lcfirst(substr($columnFunc, 0, -8) . 'Increments');
            return '$table->' . $columnFunc . '(' . implode(', ', $columnArgs) . ');';
        } else if ($category == self::COLUMN_TYPE_INTEGER || $category == self::COLUMN_TYPE_FLOAT) {
            if (strpos($this->COLUMN_TYPE, 'unsigned') !== false) {
                $columnFunc = 'unsigned' . ucfirst($columnFunc);
            }
        }
        $content = '$table->' . $columnFunc . '(' . implode(', ', $columnArgs) . ')';
        if ($this->IS_NULLABLE == 'YES') {
            $content .= '->nullable()';
        }
        if ($this->COLUMN_DEFAULT != null) {
            if (!in_array($category, [self::COLUMN_TYPE_INTEGER, self::COLUMN_TYPE_FLOAT])) {
                $defaultValue = self::quote($this->COLUMN_DEFAULT);
            } else {
                $defaultValue = $this->COLUMN_DEFAULT;
            }
            $content .= '->default(' . $defaultValue . ')';
        }
        // ->useCurrent()
        if ($this->COLUMN_KEY == 'PRI') {
            $content .= '->primary()';
        }
        if ($this->COLUMN_COMMENT) {
            $content .= '->comment(' . self::quote($this->COLUMN_COMMENT) . ')';
        }
        return $content . ';';
    }

    /**
     * Quote text.
     *
     * @param string $text Input text
     *
     * @return string
     */
    protected static function quote(string $text): string
    {
        return '\'' . $text . '\'';
    }
}
