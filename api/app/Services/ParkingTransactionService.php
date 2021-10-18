<?php


namespace App\Services;


use App\Models\ParkingTransaction;
use App\Repositories\EntryPointRepository;
use App\Repositories\ParkingTransactionRepository;
use App\Repositories\SlotRepository;
use App\Repositories\VehicleRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
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

    public function getList(){
        return $this->response(200, "view.200", $this->repository->getList());
    }

    public function create($data) {
        //find vacant slot
        $entryPointId = $data['entry_point_id'];
        $vehicleData = $data['vehicle'];

        $slot = $this->slotRepository->assignSlot($entryPointId,  $vehicleData['type']);
        if(is_null($slot)){
            return $this->response(400, "create.slot_not_exist", null);
        }

        $vehicle = $this->vehicleRepository->find('plate_no', $vehicleData['plate_no']);
        if(is_null($vehicle)){
            //record vehicle
            $vehicle = $this->vehicleRepository->create([
                'plate_no' => $vehicleData['plate_no'],
                'type' => $vehicleData['type'],
            ]);
        }

        $createTime = Carbon::now()->toDateTimeString();

        if(!$data['use_server_time']){
            $createTime = $data['date'] . ' ' . $data['time'];
            $createTime = Carbon::parse($createTime)->toDateTimeString();
        }

        //create transactions
        $created = $this->repository->create([
            'reference' => Str::random(8),
            'vehicle_id' => $vehicle->id,
            'slot_id' => $slot->id,
            'created_at' => $createTime
        ]);

        if($created){
            //update slot to not vacant
            $slot->is_vacant = false;
            $slot->save();
        }

        return $this->response(200, "create.200", [
            "transaction" => $created,
            "slot" => $slot
        ]);
    }

    public function unpark($reference, $data){

        $transaction = $this->repository->find('reference', $reference);
        if(is_null($transaction)){
            return $this->response(400, "unpark.transaction_not_exist", null);
        }

        $slot = $transaction->slot;
        if($transaction->status == ParkingTransaction::IN_PROCESS){
            //FOR TESTING THE EXIT TIME
            $exitTime = Carbon::now();
            if(!$data['use_server_time']){
                $dataExitTime = $data['date'] . ' ' . $data['time'];
                $exitTime = Carbon::parse($dataExitTime);
            }

            $timeDetails = $this->consumedTimeDetails($transaction->created_at, $exitTime);
            $description = $this->generateDescription($timeDetails);

            $currentRate = $this->calculateTotalRate($transaction->slot, $timeDetails);
            $previousTransactionRate = $this->calculatePreviousRate($transaction);
            Log::debug('current_rate: ', [$currentRate]);
            Log::debug('previous_rate: ', [$previousTransactionRate]);
            //disable flat rate for continues charges
            if($previousTransactionRate > 0){
                $currentRate = $currentRate - flat_rate();
            }

            $transaction->rate = $currentRate + $previousTransactionRate;
            $transaction->exit_time = $exitTime->toDateTimeString();
            $transaction->status = ParkingTransaction::CHARGED;
            $transaction->description = $description;
            $transaction->save();

            $slot->is_vacant = true;
            $slot->save();

            return $this->response(200, "unpark.200", [
                "transaction" => $transaction
            ]);
        }

        return $this->response(400, "unpark.400", [
            "transaction" => null
        ]);
    }

    private function generateDescription($data){
        $exceedingHours = 0;

        $desc = "Exceeded - ";
        if($data['day'] > 0){
            $desc .= " Day/s: {$data['day']}";
        }

        if($data['minute'] > 0){
            $exceedingHours += 1;
        }
        if($data['hour'] > fixed_hour()){
            $exceedingHours = $exceedingHours + ($data['hour'] - fixed_hour());
            $desc .= " hour/s: {$exceedingHours}";
        }
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

    private function calculateTotalRate($slot, $consumedTimeData){
        $totalRate = flat_rate();
        $exceedingHours = 0;

        if($consumedTimeData['day'] > 0){
            $totalRate = $totalRate + ($consumedTimeData['day'] * whole_day_rate());
        }

        if($consumedTimeData['minute'] > 0){
            $exceedingHours += 1;
        }

        Log::debug('consumed_data', $consumedTimeData);
        if($consumedTimeData['hour'] > fixed_hour()){
            $hourlyRateByVehicleSize = $slot->exceeding_hourly_rate;
            $exceedingHours = $exceedingHours + ($consumedTimeData['hour'] - fixed_hour());
            $totalRate = $totalRate + ($exceedingHours * $hourlyRateByVehicleSize);
        }
        return $totalRate;
    }

    private function calculatePreviousRate($currentTransaction){
        $vehicle = $currentTransaction->vehicle;

        //check if a vehicle has a previous charged transaction for the day
        $latestTransaction = $this->repository->vehicleLatestChargedTransaction($vehicle->id);
        Log::debug('latest transaction', [$latestTransaction]);
        if(!is_null($latestTransaction)){
            Log::debug('exit time', [$latestTransaction->exit_time]);
            Log::debug('entry time', [$currentTransaction->created_at]);
            $exitDateTime = Carbon::parse($latestTransaction->exit_time);
            $timeDetails = $this->consumedTimeDetails($currentTransaction->created_at, $exitDateTime);
            Log::debug('time details: ', [$timeDetails]);

            if($timeDetails['day'] > 0){
                return 0;
            }

            if($timeDetails['hour'] > 1){
                return 0;
            }

            return $currentTransaction->slot->exceeding_hourly_rate;

        }
        return 0;
    }
}
