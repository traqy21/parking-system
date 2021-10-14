<?php


namespace App\Services;


use App\Models\ParkingTransaction;
use App\Repositories\EntryPointRepository;
use App\Repositories\ParkingTransactionRepository;
use App\Repositories\SlotRepository;
use App\Repositories\VehicleRepository;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ParkingTransactionService extends AbstractService
{
    protected $vehicleRepository;
    protected $entryPointRepository;
    protected $slotRepository;
    public function __construct(
        ParkingTransactionRepository $repository,
        VehicleRepository $vehicleRepository,
        EntryPointRepository $entryPointRepository,
        SlotRepository $slotRepository)
    {
        $this->vehicleRepository = $vehicleRepository;
        $this->entryPointRepository = $entryPointRepository;
        $this->slotRepository = $slotRepository;
        $this->module = 'parking_transaction';
        parent::__construct($repository);
    }

    public function create($data) {
        //find vacant slot
        $entryPointId = $data['entry_point_id'];
        $vehicleData = $data['vehicle'];

        $slot = $this->slotRepository->assignSlot($entryPointId,  $vehicleData['type']);
        if(is_null($slot)){
            return $this->response(400, "create.slot_not_exist", null);
        }

        //record vehicle
        $vehicle = $this->vehicleRepository->create([
            'plate_no' => $vehicleData['plate_no'],
            'type' => $vehicleData['type'],
        ]);

        //create transactions
        $created = $this->repository->create([
            'reference' => Str::random(8),
            'vehicle_id' => $vehicle->id,
            'slot_id' => $slot->id,
        ]);

        if($created){
            //update slot to not vacant
            $slot->is_vacant = false;
            $slot->save();
        }

        return $this->response(200, "create.200", [
            "reference" => $created->reference,
            "slot_no" => $slot->slot_no
        ]);
    }

    public function unpark($reference){
        $transaction = $this->repository->find('reference', $reference);
        if(is_null($transaction)){
            return $this->response(400, "unpark.transaction_not_exist", null);
        }

        $slot = $transaction->slot;
        if($transaction->status == ParkingTransaction::IN_PROCESS){
            $exitTime = Carbon::now();
            $timeDetails = $this->consumedTimeDetails($transaction->created_at, $exitTime);
            $description = $this->generateDescription($timeDetails);

            $rate = $this->calculateTotalRate($transaction->vehicle->size, $timeDetails);

            $transaction->rate = $rate;
            $transaction->exit_time = $exitTime->toDateTimeString();
            $transaction->status = ParkingTransaction::CHARGED;
            $transaction->description = $description;

            $transaction->save();

            $slot->is_vacant = true;
            $slot->save();

            return $this->response(200, "unpark.200", [
                "charged_rate" => $transaction->rate
            ]);
        }

        return $this->response(200, "unpark.200", [
            "charged_rate" => $transaction->rate
        ]);
    }

    private function generateDescription($data){
        $exceedingHours = 0;

        $desc = "Consumed";
        if($data['day'] > 0){
            $desc .= " {$data['day']} days";
        }

        if($data['minute'] > 0){
            $exceedingHours += 1;
        }
        if($data['hour'] > 0){
            $exceedingHours = $exceedingHours + ($data['hour'] - fixed_hour());
        }

        $desc .= " and {$exceedingHours} hour/s";

        return $desc;
    }


    private function consumedTimeDetails($entryTime, $exitTime){
        $entryTime = Carbon::parse($entryTime);
        $consumedTimeObj = $entryTime->diff($exitTime);
        return [
            "day" => $consumedTimeObj->d,
            "hour" => $consumedTimeObj->h,
            "minute" => $consumedTimeObj->i
        ];
    }

    private function calculateTotalRate($vehicleSize, $consumedTimeData){
        $totalRate = flat_rate();
        $exceedingHours = 0;
        if($consumedTimeData['day'] > 0){
            $totalRate = $totalRate + ($consumedTimeData['day'] * whole_day_rate());
        }

        if($consumedTimeData['minute'] > 0){
            $exceedingHours += 1;
        }

        if($consumedTimeData['hour'] > 0){
            $hourlyRateByVehicleSize = exceeding_rate($vehicleSize);
            $exceedingHours = $exceedingHours + ($consumedTimeData['hour'] - fixed_hour());
            $totalRate = $totalRate + ($exceedingHours * $hourlyRateByVehicleSize);
        }

        return $totalRate;
    }
}
