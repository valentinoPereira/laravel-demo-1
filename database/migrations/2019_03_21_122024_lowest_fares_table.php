<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class LowestFaresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lowest_fairs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('from_airport');
            $table->string('to_airport');
            $table->string('lowest_fare')->default('0');
            $table->timestamps();
        });

        DB::table('lowest_fairs')->insert(
            array(
                array(
                    'from_airport' => 'BOM',
                    'to_airport' => 'NYC',
                ),
                array(
                    'from_airport' => 'BOM',
                    'to_airport' => 'SFO',
                ),
                array(
                    'from_airport' => 'SFO',
                    'to_airport' => 'BOM',
                ),
                array(
                    'from_airport' => 'NYC',
                    'to_airport' => 'BOM',
                )
            )
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
