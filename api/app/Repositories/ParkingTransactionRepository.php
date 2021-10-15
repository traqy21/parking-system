<?php


namespace App\Repositories;


use App\Models\ParkingTransaction;

class ParkingTransactionRepository extends AbstractRepository
{
    public function __construct(ParkingTransaction $model)
    {
        parent::__construct($model);
    }

    public function vehicleLatestChargedTransaction($vehicleId){
        return $this->model
            ->where('vehicle_id', $vehicleId)
            ->where('status', ParkingTransaction::CHARGED)
            ->orderBy('exit_time', 'desc')
            ->first();
    }
}
