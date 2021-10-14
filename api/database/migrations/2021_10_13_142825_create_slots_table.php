<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSlotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('slots', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('entry_point_id');
            $table->unsignedInteger('slot_no');
            $table->unsignedInteger('size');
            $table->unsignedInteger('distance');    //from entry point
            $table->float('exceeding_hourly_rate');
            $table->boolean('is_vacant')->default(true);


            $table->foreign('entry_point_id')
                ->references('id')
                ->on('entry_points')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('slots');
    }
}
