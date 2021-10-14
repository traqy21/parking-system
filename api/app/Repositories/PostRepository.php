<?php


namespace App\Repositories;


use App\Models\CoreModel;
use App\Models\Post;
use Illuminate\Database\Eloquent\Model;

class PostRepository extends AbstractRepository
{
    public function __construct(Post $model)
    {
        parent::__construct($model);
    }

    public function view(CoreModel $model)
    {
        return $model->with([
            'comments' => function ($query){
                $query->where('layer', 1)->orderBy('created_at', 'desc');
            },
            'comments.replies' => function ($query){
                $query->orderBy('created_at', 'desc');
            },
            'comments.replies.replies'=> function ($query){
                $query->orderBy('created_at', 'desc');
            },
        ])->first();
    }
}
