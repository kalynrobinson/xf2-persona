<?php

namespace Shinka\Persona\Service\Thread;

/**
 * Adds author field.
 */
class Editor extends XFCP_Editor
{
    /** @var \XF\Entity\User $author */
    protected $author;

    public function setAuthor(\XF\Entity\User $user) {
        $this->author = $user;
    }

    /**
     * Replace thread's user with specified author.
     * 
     * @return \XF\Entity\Thread
     */
    protected function _save()
    {
        $thread = parent::_save();
        $user_id = $thread->user_id;
        $author = $this->author;

        if ($author && $author->user_id !== $user_id) {
            $thread->user_id = $author->user_id;
            $thread->username = $author->username;

            // Update last post if applicable
            if ($thread->first_post_id === $thread->last_post_id) {
                $thread->last_post_user_id = $author->user_id;
                $thread->last_post_username = $author->username;
            }

            $thread->save();              
        }

        return $thread;
    }
}