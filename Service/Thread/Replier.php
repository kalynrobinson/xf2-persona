<?php

namespace Shinka\Persona\Service\Thread;

/**
 * Adds author field.
 */
class Replier extends XFCP_Replier
{
    /** @var \XF\Entity\User $author */
    protected $author;

    public function setAuthor(\XF\Entity\User $user) {
        $this->author = $user;
    }

    /**
     * Replace post's user with specified author.
     * 
     * @return \XF\Entity\Post
     */
    protected function _save()
    {
        $post = parent::_save();
        $author = $this->author;

        if ($author && $author->user_id !== $post->user_id) {
            $post->user_id = $author->user_id;
            $post->username = $author->username;
            $post->save();
        }

        return $post;
    }
}