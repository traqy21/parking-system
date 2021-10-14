<?php

use Illuminate\Database\Seeder;
use Faker\Generator as Faker;
class EntryPointSeeder extends Seeder
{
    protected $faker;
    protected $slotNo = 1;
    public function __construct(Faker $faker)
    {
        $this->faker = $faker;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //create 3 entry points
        $entryPoints = 3;
        for ($i=1; $i<= $entryPoints; $i++){
            $entryPoint = \App\Models\EntryPoint::firstOrCreate([ 'name' => "Entrance {$i}"])->fresh();
            $this->addSlot($entryPoint);
        }
    }

    private function addSlot($entrypoint){
        $maxDistance = 10;
        for ($i=1; $i<= $maxDistance; $i++){
            $size = $this->faker->randomElement([0,1,2]);
            $exceedingRate =
            \App\Models\Slot::create([
                'entry_point_id' => $entrypoint->id,
                'size' => $size,
                'slot_no' => $this->slotNo,
                'distance' => $i,
                'exceeding_hourly_rate' => exceeding_rate($size),
            ]);

            $this->slotNo++;
        }
    }
}
