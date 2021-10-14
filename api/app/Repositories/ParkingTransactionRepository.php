<?php


namespace App\Repositories;


use App\Models\ParkingTransaction;

class ParkingTransactionRepository extends AbstractRepository
{
    public function __construct(ParkingTransaction $model)
    {
        parent::__construct($model);
    }
}
