<?php

namespace Shinka\Persona\Entity;

class User extends XFCP_User
{

    /**
     * Maps Personas and Parents, then map to User.
     *
     * @return \XF\Mvc\Entity\AbstractCollection
     */
    public function getPersonas()
    {
        $pivot = $this->Personas_->filter(function ($persona) {
            return $persona->approved;
        })->pluckNamed('User');
        $parents = $this->Parents_->filter(function ($persona) {
            return $persona->approved;
        })->pluckNamed('Parent');
        return $pivot->merge($parents);
    }

    /**
     * Maps Personas and Parents, then map to User.
     *
     * @return \XF\Mvc\Entity\AbstractCollection
     */
    public function getParents()
    {
        return $this->Parents_->filter(function ($persona) {
            return $persona->approved;
        })->pluckNamed('Parent');
    }

    /**
     * @return \XF\Mvc\Entity\AbstractCollection
     */
    public function getPendingPersonas()
    {
        return $this->Parents_->filter(function ($persona) {
            return !$persona->approved;
        })->pluckNamed('Parent');
    }

    /**
     * @return \XF\Mvc\Entity\AbstractCollection
     */
    public function getAwaitingPersonas()
    {
        return $this->Personas_->filter(function ($persona) {
            return !$persona->approved;
        })->pluckNamed('User');
    }

    /**
     * Merges Parents and Personas.
     *
     * @return \XF\Mvc\Entity\AbstractCollection
     */
    public function getPersonaPivots()
    {
        return $this->Personas_->merge($this->Parents_);
    }
}
