<?php

namespace Shinka\Persona\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class Persona extends Repository
{
    /**
     * @return Finder
     */
    public function findPersonaByParentAndUser(int $parent_id, $persona_id)
    {
        // $finder = $this->finder('Shinka\Persona:Persona');
        // $conditions = [
        //     ['parent_id', '=', $parent_id],
        //     ['persona_id', '=', $parent_id],
        // ];
        // $finder
        //     ->where('user_id', '=', $user_id)
        //     ->limit(1);

        // return $finder;
    }
}