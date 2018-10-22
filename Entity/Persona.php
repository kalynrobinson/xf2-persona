<?php

namespace Shinka\Persona\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int thread_id
 * @property string field_id
 * @property string field_value
 */
class Persona extends Entity
{
    public static function getStructure(Structure $structure)
    {
        $structure->table = 'xf_shinka_persona';
        $structure->shortName = 'Shinka\Persona:Persona';
        $structure->primaryKey = 'persona_id';
        $structure->contentType = 'persona';
        $structure->columns = [
            'persona_id' => ['type' => self::UINT, 'autoIncrement' => true],
            'parent_id' => ['type' => self::UINT, 'required' => true],
            'user_id' => ['type' => self::UINT, 'required' => true],
            'approved' => ['type' => self::BOOL, 'required' => false, 'default' => false],
        ];
        $structure->relations = [
            'Parent' => [
                'entity' => 'XF:User',
                'type' => self::TO_ONE,
                'conditions' => [['user_id', '=', '$parent_id']],
                'primary' => true,
            ],
            'User' => [
                'entity' => 'XF:User',
                'type' => self::TO_ONE,
                'conditions' => [['user_id', '=', '$user_id']],
                'primary' => true,
            ],
        ];
        $structure->getters = [];

        return $structure;
    }
}
