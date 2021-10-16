<?php


namespace App\Services;


use App\Repositories\EntryPointRepository;

class EntryPointService extends AbstractService
{
    public function __construct(EntryPointRepository $repository)
    {
        $this->module = 'entry_point';
        parent::__construct($repository);
    }

    public function getList(){
        return $this->response(200, "view.200", $this->repository->getList());
    }
}
