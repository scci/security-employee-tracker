<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDutySwapsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('duty_swaps', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('imageable_id');
            $table->string('imageable_type');
            $table->integer('duty_id')->unsigned();
            $table->date('date');
            $table->foreign('duty_id')->references('id')->on('duties')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('duty_swaps');
    }
}
