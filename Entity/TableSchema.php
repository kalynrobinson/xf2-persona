<?php

namespace Shinka\Persona\Entity;

/**
 * Manages table schemas, including wrappers for creating and dropping
 * the table.
 * 
 * @see SchemaManager 
 */
class TableSchema {
    /** @var string Name of table. Should include <c>xf_</c> prefix. */
    public $name;

    /** @var string|array Primary key(s). Accepts composite keys as an array. */
    public $primary_key;

    /** @var array List of columns, where each column has the keys 
     * <c>name</c> and <c>type</c>.
     */
    public $columns = [];

    function __construct(string $name, $primary_key, array $columns) 
    {
        $this->name = $name;
        $this->primary_key = $primary_key;
        $this->columns = $columns;
    }

    /**
     * Instantiates table in database.
     *
     * @return void
     */
    function create() {
		\XF::db()->getSchemaManager()->createTable($this->name, function(\XF\Db\Schema\Create $table)
		{
            foreach ($this->columns as $column) {
                $col = $table->addColumn($column['name'], $column['type']);
                if (isset($column['default'])) {
                    $col->setDefault($column['default']);
                }
            }

			$table->addPrimaryKey($this->primary_key);
		});
    }

    /**
     * Drops table from database.
     */
    function drop() {
		\XF::db()->getSchemaManager()->dropTable($this->name);
    }
}