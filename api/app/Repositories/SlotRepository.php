<?php


namespace App\Repositories;


use App\Models\Slot;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Log;

class SlotRepository extends AbstractRepository
{
    public function __construct(Slot $model)
    {
        parent::__construct($model);
    }

    public function assignSlot($entryPointId, $vehicleType){
        $query = $this->model
            ->where('is_vacant', true)
            ->where('entry_point_id', $entryPointId);

        //(b) M vehicles can park in MP and LP parking spaces; and
        if($vehicleType ==  Vehicle::MEDIUM){
            $query = $query->whereBetween('size', [Slot::MEDIUM, Slot::LARGE]);
        }

        //(c) L vehicles can park only in LP parking spaces.
        if($vehicleType ==  Vehicle::LARGE){
            $query = $query->where('size', Slot::LARGE);
        }

        //(a) S vehicles can park in SP, MP, and LP parking spaces;
        $query = $query->orderBy('distance');
        return $query->first();
    }
}
