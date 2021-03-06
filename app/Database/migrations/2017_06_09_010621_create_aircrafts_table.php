<?php

use \App\Models\Enums\AircraftState;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAircraftsTable extends Migration
{
    public function up()
    {
        Schema::create('aircraft', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('subfleet_id');
            $table->string('icao', 4)->nullable();
            $table->string('airport_id', 5)->nullable();
            $table->timestamp('landing_time')->nullable();
            $table->string('hex_code', 10)->nullable();
            $table->string('name', 50);
            $table->string('registration', 10)->nullable();
            $table->string('tail_number', 10)->nullable();
            $table->unsignedDecimal('zfw', 12)->nullable()->default(0);
            $table->unsignedBigInteger('flight_time')->nullable()->default(0);
            $table->boolean('active')->default(true);
            $table->unsignedTinyInteger('state')->default(AircraftState::PARKED);
            $table->timestamps();

            $table->unique('registration');
            $table->index('airport_id');
        });

    }

    public function down()
    {
        Schema::dropIfExists('aircraft');
    }
}
