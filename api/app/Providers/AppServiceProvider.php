<?php

namespace App\Providers;

use App\Models\ParkingTransaction;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('validateAlreadyParked', function ($attribute, $value, $parameters, $validator) {
             $plateNo = $value;
             $vehicle = Vehicle::where('plate_no', $plateNo)->first();
             if(is_null($vehicle)){
                 return true;
             }

             $transaction = ParkingTransaction::where('vehicle_id', $vehicle->id)->where('status', ParkingTransaction::IN_PROCESS)
                 ->first();

             if(is_null($transaction)){
                 return true;
             }

             return false;
        });

        Validator::extend('validateVehicleType', function ($attribute, $value, $parameters, $validator) {
            $request = request()->all();

            $plateNo = $request['vehicle']['plate_no'];
            $type = $request['vehicle']['type'];
            $vehicle = Vehicle::where('plate_no', $plateNo)->first();

            if(is_null($vehicle)){
                return true;
            }

            if((int) $vehicle->type != $type){
                return false;
            }

            return true;
        });


    }
}
