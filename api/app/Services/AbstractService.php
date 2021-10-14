<?php


namespace App\Services;


use App\Repositories\AbstractRepository;

abstract class AbstractService
{
    protected $repository;
    protected $module = 'default';

    public function __construct(AbstractRepository $repository)
    {
        $this->repository = $repository;
    }

    public function create($data) {
        $created = $this->repository->create($data);
        return $this->response(200, "create.200", $created);
    }

    public function update($model, $data) {
        $updated = $this->repository->update($model, $data);
        return $this->response(200, "update.200", $updated);
    }

    public function delete($model) {
        $deleted = $this->repository->delete($model);
        return $this->response(200, "delete.200", $deleted);
    }

    public function view($model) {
        $model = $this->repository->view($model);
        return $this->response(200, "view.200", $model);
    }

    public function find($field, $value) {
        return $this->repository->find($field, $value);
    }

    public function deleteAll($field, $value) {
        $deleted = $this->repository->deleteAll($field, $value);
        return $this->response(200, "delete.200", $deleted);
    }
    public function response($status, $message, $data = []){
        return (object) [
            "status" => $status,
            "message" => __("messages.{$this->module}.{$message}"),
            "data" => $data
        ];
    }
}
