<?php
namespace Tests\Feature\SysAdmin;

use App\Models\EntryPoint;
use App\Models\ParkingTransaction;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class ParkingTest extends TestCase
{
    public function testEntryPointsList(){
        $response = $this->getJson("api/entry-points");
        $this->debug($response);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'message'
        ]);
    }

    public function testParkingTransactionList(){
        $response = $this->getJson("api/transactions");
        $this->debug($response);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'message'
        ]);
    }
    //test parking
    public function testParkVehicle(){
        $entryPoint = EntryPoint::first();
        $response = $this->postJson("api/transactions", []);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $plateNo = Str::random(10);

        //invalid type
        $response = $this->postJson("api/transactions", [
            'vehicle' => [
                'plate_no' => $plateNo,
                'type' => 3, //0-small, 1-medium, 2-large
            ],
            'entry_point_id' => $entryPoint->id,
            "date" => Carbon::now()->toDateString(),
            "time" => Carbon::now()->toTimeString(),
            "use_server_time" => false,
        ]);
        $response->assertStatus(    Response::HTTP_UNPROCESSABLE_ENTITY);

        //201
        $response = $this->postJson("api/transactions", [
            'vehicle' => [
                'plate_no' => $plateNo,
                'type' => Vehicle::SMALL, //0-small, 1-medium, 2-large
            ],
            'entry_point_id' => $entryPoint->id,
            "date" => Carbon::now()->toDateString(),
            "time" => Carbon::now()->toTimeString(),
            "use_server_time" => true,
        ]);
        $this->debug($response);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'message'
        ]);
    }

    public function testUnparkVehicle(){
        //park vehicle first
        $entryPoint = EntryPoint::first();
        $response = $this->postJson("api/transactions", []);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $plateNo = Str::random(10);
        $response = $this->postJson("api/transactions", [
            'vehicle' => [
                'plate_no' => $plateNo,
                'type' => Vehicle::SMALL, //0-small, 1-medium, 2-large
            ],
            'entry_point_id' => $entryPoint->id,
            "date" => Carbon::now()->toDateString(),
            "time" => Carbon::now()->toTimeString(),
            "use_server_time" => false,
        ]);
        $data = $this->decode($response)->data;
        $transaction = $data->transaction;
        //invalid type
        $response = $this->putJson("api/transactions/{$transaction->reference}", [
            "date" => Carbon::now()->toDateString(),
            "time" => Carbon::now()->toTimeString(),
            "use_server_time" => true,
        ]);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'message'
        ]);
    }

    public function testContinuesCharge(){
        $entryPoint = EntryPoint::first();

        //Car A park
        $carAplateNo = Str::random(10);
        $response = $this->postJson("api/transactions", [
            'vehicle' => [
                'plate_no' => $carAplateNo,
                'type' => Vehicle::SMALL, //0-small, 1-medium, 2-large
            ],
            'entry_point_id' => $entryPoint->id,
        ]);

        $carAParkData = $this->decode($response)->data;

        //update created_at of Car A transaction
        $carAtransaction = ParkingTransaction::where('reference', $carAParkData->reference)->first();
        $carAtransaction->created_at = Carbon::parse($carAtransaction->created_at)->subHour(1)->subDay()->toDateTimeString();
        $carAtransaction->save();

        //Car A unpark
        $response = $this->putJson("api/transactions/{$carAParkData->reference}");
        Log::debug('CarA unpark: ', [$this->decode($response)->data]);

        //Car B park
        $carBplateNo = Str::random(10);
        $response = $this->postJson("api/transactions", [
            'vehicle' => [
                'plate_no' => $carBplateNo,
                'type' => Vehicle::SMALL, //0-small, 1-medium, 2-large
            ],
            'entry_point_id' => $entryPoint->id
        ]);

        $carBParkData = $this->decode($response)->data;

        //Car A park again and add 1 hour to entry time
        $response = $this->postJson("api/transactions", [
            'vehicle' => [
                'plate_no' => $carAplateNo,
                'type' => Vehicle::SMALL, //0-small, 1-medium, 2-large
            ],
            'entry_point_id' => $entryPoint->id
        ]);

        $carAParkData = $this->decode($response)->data;
        $carAtransaction = ParkingTransaction::where('reference', $carAParkData->reference)->first();
        $carAtransaction->created_at = Carbon::parse($carAtransaction->created_at)->addHour()->toDateTimeString();
        $carAtransaction->save();

        //Car A unpark
//        $response = $this->putJson("api/transactions/{$carAParkData->reference}");
//        Log::debug('CarA unpark again: ', [$this->decode($response)->data]);

    }

    public function testManualValuesUnparkVehicle(){
        //invalid type
        $response = $this->putJson("api/transactions/zhhjKnGS", [
            "date" => Carbon::now()->toDateString(),
            "time" => Carbon::now()->toTimeString(),
            "use_server_time" => true,
        ]);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'message'
        ]);
    }


}
