<?php

namespace Shinka\Persona\Entity;

class User extends XFCP_User {

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
     * Maps Personas to Personas->User.
     * 
     * @return \XF\Mvc\Entity\AbstractCollection
     */
    public function getPendingPersonas()
    {
        $pivot = $this->Parents_->filter(function ($persona) {
            return !$persona->approved;
        });
        return $pivot->pluckNamed('Parent');
    }

    /**
     * Maps Personas and Parents, then map to User.
     * 
     * @return \XF\Mvc\Entity\AbstractCollection
     */
    public function getPersonaPivots()
    {
        $pivot = $this->Personas_->filter(function ($persona) {
            return $persona->approved;
        });
        $parents = $this->Parents_->filter(function ($persona) {
            return $persona->approved;
        });
        return $pivot->merge($parents);
    }
}



