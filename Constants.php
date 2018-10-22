<?php

namespace Shinka\Persona;

use Shinka\Persona\Entity\TableSchema;

/**
 * Convenient access to constants.
 */
class Constants
{
    /** @var string Database table name */
    public static $table = 'xf_shinka_persona';

    /**
     * @return \Shinka\Persona\Entity\TableSchema
     */
    public static function tableSchema()
    {
        $data = [
            'name' => Constants::$table,
            'primary_key' => 'persona_id',
            'columns' => [
                [
                    'name' => 'persona_id',
                    'type' => 'int',
                    'autoIncrement' => true
                ],
                [
                    'name' => 'parent_id',
                    'type' => 'int',
                ],
                [
                    'name' => 'user_id',
                    'type' => 'int',
                ],
                [
                    'name' => 'approved',
                    'type' => 'tinyint',
                    'default' => 0,
                ]
            ],
        ];

        return new TableSchema($data['name'], $data['primary_key'], $data['columns']);
    }
}
