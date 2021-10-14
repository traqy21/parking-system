<?php
namespace Tests\Feature\SysAdmin;

use App\Models\EntryPoint;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class ParkingTest extends TestCase
{

    public function testViewPost(){
        $post = Post::first();
        $response = $this->getJson("api/posts/{$post->id}", []);
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
            'entry_point_id' => $entryPoint->id
        ]);
        $response->assertStatus(    Response::HTTP_UNPROCESSABLE_ENTITY);

        //201
        $response = $this->postJson("api/transactions", [
            'vehicle' => [
                'plate_no' => $plateNo,
                'type' => Vehicle::SMALL, //0-small, 1-medium, 2-large
            ],
            'entry_point_id' => $entryPoint->id
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
            'entry_point_id' => $entryPoint->id
        ]);
        $transaction = $this->decode($response)->data;

        //invalid type
        $response = $this->putJson("api/transactions/{$transaction->reference}");
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'message'
        ]);
    }


    public function testUnparkVehicleManualValues(){
        //invalid type
        $response = $this->putJson("api/transactions/IrkIUpuI");
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'message'
        ]);
    }

}
