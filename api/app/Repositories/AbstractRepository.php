<?php


namespace App\Repositories;

use App\Models\CoreModel;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractRepository
{
    const APPENDS = [];

    protected $model;

    public function __construct(Model $model) {
        $this->model = $model;
    }

    public function create($data) {
        return $this->model->create($data);
    }

    public function insert($data) {
        return $this->model->insert($data);
    }

    public function update(CoreModel $model, $data) {
        $model->fill($data)->save();
        return $model;
    }

    public function delete(CoreModel $model) {
       return $model->delete();
    }

    public function view(CoreModel $model) {
        return $model->append(static::APPENDS);
    }

    public function find($field, $value) {
        return $this->model->where($field, $value)->first();
    }

    public function deleteAll($field, $value) {
        return $this->model->where($field, $value)->delete();
    }
}
