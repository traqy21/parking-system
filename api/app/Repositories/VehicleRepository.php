<?php


namespace App\Repositories;


use App\Models\Vehicle;

class VehicleRepository extends AbstractRepository
{
    public function __construct(Vehicle $model)
    {
        parent::__construct($model);
    }
}
