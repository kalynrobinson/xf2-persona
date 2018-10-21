<?php

namespace Shinka\Persona\XF\Listener;

use XF\Mvc\Entity\Entity;

class Listener
{
    public static function userEntityStructure(\XF\Mvc\Entity\Manager $em, \XF\Mvc\Entity\Structure &$structure)
    {
        $structure->relations['Personas'] = [
            'entity' => 'Shinka\Persona:Persona',
            'type' => Entity::TO_MANY,
            'conditions' => [['parent_id', '=', '$user_id']],
            'primary' => true,
        ];
        $structure->relations['Parents'] = [
            'entity' => 'Shinka\Persona:Persona',
            'type' => Entity::TO_MANY,
            'conditions' => [['persona_id', '=', '$user_id']],
            'primary' => true,
        ];

        $structure->getters['Personas'] = true;
        $structure->getters['personaPivots'] = true;
        $structure->getters['pendingPersonas'] = true;
        $structure->getters['awaitingPersonas'] = true;
    }
}
