<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParkingTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parking_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->dateTime('exit_time');
            $table->float('rate')->default(0);
            $table->uuid('vehicle_id');
            $table->uuid('slot_id');

            $table->foreign('slot_id')
                ->references('id')
                ->on('slots')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('vehicle_id')
                ->references('id')
                ->on('vehicles')
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
        Schema::dropIfExists('parking_transactions');
    }
}
