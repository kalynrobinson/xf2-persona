<?php

namespace Shinka\Persona\Service\Thread;

/**
 * Adds author field.
 */
class Creator extends XFCP_Creator
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
        $author = $this->author;

        if ($author && $author->user_id !== $thread->user_id) {
            $thread->user_id = $author->user_id;
            $thread->username = $author->username;

            // Set first post's author as well
            $post = $thread->FirstPost;
            $post->user_id = $author->user_id;
            $post->username = $author->username;

            $thread->save();
        }

        return $thread;
    }
}