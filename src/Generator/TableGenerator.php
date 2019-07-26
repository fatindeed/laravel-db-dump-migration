<?php

namespace Fatindeed\LaravelDbDumpMigration\Generator;

/**
 * Table generator class
 *
 * @see https://laravel.com/docs/5.8/migrations
 */
class TableGenerator
{
    /**
     * The DataProvider instance.
     *
     * @var \Fatindeed\LaravelDbDumpMigration\Generator\DataProvider
     */
    protected $dataProvider;

    /**
     * Create a new table generator instance.
     *
     * @param \Fatindeed\LaravelDbDumpMigration\Generator\DataProvider $dataProvider The DataProvider instance
     */
    public function __construct(DataProvider $dataProvider)
    {
        $this->dataProvider = $dataProvider;
    }

    /**
     * Get Blueprint.
     *
     * @param string $prefix Indents and Spacing
     *
     * @return string
     */
    public function getBlueprint(string $prefix = ''): string
    {
        $lines = [];
        $columns = $this->dataProvider->getColumns();
        foreach ($columns as $data) {
            $column = new ColumnGenerator($data);
            $lines[] = $column->getMigrationCommand();
        }
        $indexes = $this->dataProvider->getIndexes();
        foreach ($indexes as $index) {
            if ($index->CONSTRAINT_NAME == 'PRIMARY') {
                continue;
            }
            // $table->unique('email');
            $lines[] = '$table->index(\'' . $index->COLUMN_NAME . '\');';
        }
        $content = '';
        foreach ($lines as $line) {
            $content .= $prefix . $line . PHP_EOL;
        }
        return $content;
    }
}
