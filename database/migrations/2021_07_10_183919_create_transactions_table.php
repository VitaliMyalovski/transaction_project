<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('transactions');
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('from_user_id');
            $table->bigInteger('to_user_id');
            $table->bigInteger('from_user_wallet_id');
            $table->bigInteger('to_user_wallet_id');
            $table->string('from_character', 3);
            $table->string('to_character', 3);
            $table->double('from_value',24,2);
            $table->double('to_value',24,2);
            $table->unsignedSmallInteger('type_oper');
            $table->double('from_quotation',24,6);
            $table->double('to_quotation',24,6);
            $table->smallInteger('is_done')->default(0);
            $table->timestamps();
            $table->foreign('from_user_id')->references('user_id')->on('user')->onDelete('cascade');
            $table->foreign('to_user_id')->references('user_id')->on('user')->onDelete('cascade');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
