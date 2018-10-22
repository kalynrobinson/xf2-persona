<?php

namespace Shinka\Persona\Service\Post;

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