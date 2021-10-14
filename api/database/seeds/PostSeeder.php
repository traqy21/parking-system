<?php

use Illuminate\Database\Seeder;
use Faker\Generator as Faker;
class PostSeeder extends Seeder
{
    protected $faker;
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
        \App\Models\Post::firstOrCreate([ 'title' => 'Labrador Retrievers'], [
            'description' => $this->faker->paragraph(2)
        ])->fresh();
    }
}
