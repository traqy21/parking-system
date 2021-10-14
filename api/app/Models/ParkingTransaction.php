<?php


namespace App\Models;


class ParkingTransaction extends CoreModel
{
    const IN_PROCESS = 'in_process';
    const CHARGED = 'charged';

    protected $fillable = [
        'reference',
        'vehicle_id',
        'slot_id',
        'status',
        'exit_time',
        'rate',
        'description',
    ];

    public function vehicle(){
        return $this->belongsTo(Vehicle::class);
    }

    public function slot(){
        return $this->belongsTo(Slot::class);
    }
}
