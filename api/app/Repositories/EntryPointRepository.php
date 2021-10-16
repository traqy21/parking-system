<?php


namespace App\Repositories;


use App\Models\EntryPoint;

class EntryPointRepository extends AbstractRepository
{
    public function __construct(EntryPoint $model)
    {
        parent::__construct($model);
    }

    public function getList() {
        return $this->model->with('slots')->get();
    }
}
