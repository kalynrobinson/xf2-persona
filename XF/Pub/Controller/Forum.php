<?php

namespace Shinka\Persona\XF\Pub\Controller;

class Forum extends XFCP_Forum
{
    protected $author;

    /**
     * Set author from author_id field.
     *
     * @param \XF\Entity\Forum $forum
     * @return \Shinka\Persona\Service\Thread\Creator
     */
    protected function setupThreadCreate(\XF\Entity\Forum $forum)
    {
        /** @var \Shinka\Persona\Service\Thread\Creator $creator */
        $creator = parent::setupThreadCreate($forum);
        
        $author_id = $this->filter('shinka_persona_author_id', 'uint');
        $visitor = \XF::visitor();

        if (!isset($author_id) || $author_id === $visitor->user_id) 
            return $creator;

        $persona = $visitor->Personas->filter(function ($persona) use ($author_id) {
            return $persona->user_id === $author_id;
        })->first();

        if (!$persona) return $creator;

        $creator->setAuthor($persona);

        return $creator;
    }
}