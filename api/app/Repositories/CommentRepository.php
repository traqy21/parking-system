<?php


namespace App\Repositories;


use App\Models\Comment;

class CommentRepository extends AbstractRepository
{
     public function __construct(Comment $model)
     {
         parent::__construct($model);
     }

     public function comments($id){
         return $this->model->where('to_comment_id', $id)->get();
     }
}
