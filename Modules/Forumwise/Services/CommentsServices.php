<?php

namespace Modules\Forumwise\Services;
use Modules\Forumwise\Models\Comment;

class CommentsServices
{
    public function createComment(array $data)
    {
        return Comment::create($data);
    }
}