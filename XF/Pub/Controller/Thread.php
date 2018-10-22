<?php

namespace Shinka\Persona\XF\Pub\Controller;

class Thread extends XFCP_Thread
{
    protected $author;

    /**
	 * @param \XF\Entity\Thread $thread
	 *
	 * @return \XF\Service\Thread\Replier
	 */
	protected function setupThreadReply(\XF\Entity\Thread $thread)
	{
        $replier = parent::setupThreadReply($thread);

        $author_id = $this->filter('shinka_persona_author_id', 'uint');
        $visitor = \XF::visitor();

        if (!isset($author_id) || $author_id === $visitor->user_id) 
            return $replier;

        $persona = $this->assertPersonaExists($visitor, $author_id);

        if (!$persona) return $replier;

        $replier->setAuthor($persona);
		return $replier;
    }

    protected function assertPersonaExists($user, $user_id) {
        return $user->Personas->filter(function ($persona) use ($user_id) {
            return $persona->user_id === $user_id;
        })->first();
    }
}