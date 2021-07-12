<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExchangeRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('exchange_rates');
        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->string('character', 3);
            $table->double('quotation',24,6);
            $table->date('on_date');
            $table->timestamps();
            $table->unique(['character', 'on_date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exchange_rates');
    }
}
