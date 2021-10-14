<?php

namespace App\Models;

class Comment extends CoreModel
{
    protected $fillable = [
        'post_id',
        'user_name',
        'message',
        'to_comment_id',
        'layer',
    ];

    public function replies(){
        return $this->hasMany(Comment::class, 'to_comment_id', 'id');
    }
}
