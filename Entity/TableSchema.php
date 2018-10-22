<?php

namespace Shinka\Persona\Entity;

/**
 * Manages table schemas, including wrappers for creating and dropping
 * the table.
 *
 * @see SchemaManager
 */
class TableSchema
{
    /** @var string Name of table. Should include <c>xf_</c> prefix. */
    public $name;

    /** @var string|array Primary key(s). Accepts composite keys as an array. */
    public $primary_key;

    /** @var array List of columns, where each column has the keys
     * <c>name</c> and <c>type</c>.
     */
    public $columns = [];

    public function __construct(string $name, $primary_key, array $columns)
    {
        $this->name = $name;
        $this->primary_key = $primary_key;
        $this->columns = $columns;
    }

    /**
     * Creates table in database.
     *
     * @return void
     */
    public function create()
    {
        \XF::db()->getSchemaManager()->createTable($this->name, function (\XF\Db\Schema\Create $table) {
            $table->addPrimaryKey($this->primary_key);

            foreach ($this->columns as $column) {
                $col = $table->addColumn($column['name'], $column['type']);
                if (isset($column['default'])) {
                    $col->setDefault($column['default']);
                }
                if (isset($column['nullable']) && $column['nullable']) {
                    $col->nullable();
                }
                if (isset($column['autoIncrement']) && $column['autoIncrement']) {
                    $col->autoIncrement();
                }
            }
        });
    }

    /**
     * Drops table from database.
     */
    public function drop()
    {
        \XF::db()->getSchemaManager()->dropTable($this->name);
    }
}
