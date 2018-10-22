<?php

namespace Shinka\Persona\XF\Pub\Controller;

class Post extends XFCP_Post
{
    protected $author;

    /**
     * Set author from author_id field.
     *
     * @param \XF\Entity\Post $Post
     * @return \Shinka\Persona\Service\Post\Editor
     */
    protected function setupPostEdit(\XF\Entity\Post $post)
    {
        /** @var \Shinka\Persona\Service\Post\Editor $editor */
        $editor = parent::setupPostEdit($post);
        
        $author_id = $this->filter('shinka_persona_author_id', 'uint');
        $user = $post->User;

        if (!isset($author_id) || $author_id === $user->user_id) 
            return $editor;

        $persona = $this->assertPersonaExists($user, $author_id);
        if (!$persona) return $editor;

        $editor->setAuthor($persona);
        return $editor;
    }
    
	/**
	 * @param \XF\Entity\Thread $thread
	 * @param array $threadChanges Returns a list of whether certain important thread fields are changed
	 *
	 * @return \XF\Service\Thread\Editor
	 */
	protected function setupFirstPostThreadEdit(\XF\Entity\Thread $thread, &$threadChanges) {
        $editor = parent::setupFirstPostThreadEdit($thread, $threadChanges);
        
        $author_id = $this->filter('shinka_persona_author_id', 'uint');
        $user = $thread->User;

        if (!isset($author_id) || $author_id === $user->user_id) 
            return $editor;

        $persona = $this->assertPersonaExists($user, $author_id);
        if (!$persona) return $editor;

        $editor->setAuthor($persona);
        return $editor;
    }

    protected function assertPersonaExists(\XF\Entity\User $user, int $user_id) {
        return $user->Personas->filter(function ($persona) use ($user_id) {
            return $persona->user_id === $user_id;
        })->first();
    }
}