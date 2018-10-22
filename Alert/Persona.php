<?php

namespace Shinka\Persona\Alert;

use XF\Mvc\Entity\Entity;

class Persona extends \XF\Alert\AbstractHandler
{
    public function canViewContent(Entity $entity, &$error = null)
    {
        return true;
    }
}
